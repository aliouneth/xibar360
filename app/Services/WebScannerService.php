<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

/**
 * Fetches and parses upstream RSS/Atom feeds into a normalised array shape.
 *
 * This service only reads and normalises. Persistence, de-duplication and
 * category assignment are the importer command's job, so that a network
 * problem can never leave a half-written article behind.
 */
class WebScannerService
{
    public const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    public const NS_CONTENT = 'http://purl.org/rss/1.0/modules/content/';
    public const NS_DC = 'http://purl.org/dc/elements/1.1/';
    public const NS_ATOM = 'http://www.w3.org/2005/Atom';

    /** How far ahead of the clock an upstream pubDate is tolerated, in seconds. */
    private const FUTURE_TOLERANCE = 900;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function scan(): array
    {
        $articles = [];
        $errors = [];

        foreach ($this->enabledSources() as $source) {
            try {
                $xml = $this->fetch($source['url'], $source);
                $items = $this->parse($xml, $source);

                $articles = array_merge($articles, $items);

                Log::info('news scan: source ok', [
                    'source' => $source['name'],
                    'items' => count($items),
                ]);
            } catch (Throwable $e) {
                // A single unreachable or malformed feed must not abort the
                // whole refresh, so failures are collected and reported.
                $errors[$source['name']] = $e->getMessage();

                Log::warning('news scan: source failed', [
                    'source' => $source['name'],
                    'url' => $source['url'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($errors !== []) {
            Log::info('news scan: completed with source failures', ['failures' => $errors]);
        }

        return $articles;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function enabledSources(): array
    {
        $sources = config('news_sources.sources', []);

        return array_values(array_filter($sources, fn (array $s) => ($s['enabled'] ?? true) === true));
    }

    /**
     * @return array<int, string> source name => error message
     */
    public function scanWithReport(): array
    {
        $report = ['sources' => [], 'articles' => []];

        foreach ($this->enabledSources() as $source) {
            try {
                $items = $this->parse($this->fetch($source['url'], $source), $source);

                $report['articles'] = array_merge($report['articles'], $items);
                $report['sources'][] = ['name' => $source['name'], 'ok' => true, 'count' => count($items)];
            } catch (Throwable $e) {
                $report['sources'][] = ['name' => $source['name'], 'ok' => false, 'error' => $e->getMessage()];
            }
        }

        return $report;
    }

    private function fetch(string $url, array $source = []): SimpleXMLElement
    {
        $headers = [
            'User-Agent' => config('news_sources.user_agent'),
            'Accept' => 'application/rss+xml, application/atom+xml, application/xml;q=0.9, text/xml;q=0.9, */*;q=0.8',
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
            'Upgrade-Insecure-Requests' => '1',
        ];

        // Some publishers reject both the default bot user agent and an
        // explicit Accept header, so a source may override them outright.
        if (isset($source['user_agent'])) {
            $headers = ['User-Agent' => $source['user_agent']] + ($source['headers'] ?? []);
        }

        $response = Http::withHeaders($headers)
            ->timeout(config('news_sources.timeout', 20))
            ->withOptions(['verify' => false, 'allow_redirects' => true])
            ->get($url);

        if (! $response->successful()) {
            throw new \RuntimeException("HTTP {$response->status()}");
        }

        $body = $response->body();

        if (trim($body) === '') {
            throw new \RuntimeException('Empty response body');
        }

        // Entity decoding: many of these feeds emit raw HTML entities such as
        // &rsquo; inside <description>, which is not valid XML.
        $previous = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_string(
                $body,
                SimpleXMLElement::class,
                LIBXML_NOCDATA | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET
            );

            if ($xml === false) {
                throw new \RuntimeException('Malformed XML: ' . trim(libxml_get_error() ?: 'unknown'));
            }
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        return $xml;
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array<int, array<string, mixed>>
     */
    private function parse(SimpleXMLElement $xml, array $source): array
    {
        $limit = (int) config('news_sources.per_source_limit', 15);

        $nodes = $this->itemNodes($xml);

        $articles = [];
        foreach (array_slice($nodes, 0, $limit) as $node) {
            $article = $this->normalise($node, $source);

            if ($article !== null) {
                $articles[] = $article;
            }
        }

        return $articles;
    }

    /**
     * Supports both RSS 2.0 (<item>) and Atom (<entry>).
     *
     * @return array<int, SimpleXMLElement>
     */
    private function itemNodes(SimpleXMLElement $xml): array
    {
        $nodes = [];

        if (isset($xml->channel->item)) {
            foreach ($xml->channel->item as $item) {
                $nodes[] = $item;
            }
        } elseif (isset($xml->item)) {
            foreach ($xml->item as $item) {
                $nodes[] = $item;
            }
        }

        if ($nodes === [] && isset($xml->entry)) {
            foreach ($xml->entry as $entry) {
                $nodes[] = $entry;
            }
        }

        return $nodes;
    }

    /**
     * @param  array<string, mixed>  $source
     * @return array<string, mixed>|null
     */
    private function normalise(SimpleXMLElement $node, array $source): ?array
    {
        $title = $this->clean($this->firstText($node, ['title']));
        $link = $this->firstLink($node);

        if ($title === '' || $link === '') {
            return null;
        }

        $content = $this->clean($this->firstText($node, ['content:encoded', 'description', 'summary']));
        $summary = $this->clean($this->firstText($node, ['description', 'summary']));

        if ($summary === '' || $summary === $content) {
            $summary = Str::limit($content, 320);
        }

        if ($summary === '' && $content === '') {
            return null;
        }

        // external_id stores a SHA-256 digest, not the raw guid: feed guids are
        // unbounded (Google News emits ~500 character blobs) and the column
        // carries a unique index. Deterministic, so refreshes de-duplicate.
        $externalId = $this->clean($this->firstText($node, ['guid', 'id']));
        $externalId = $externalId !== '' ? $externalId : $link;
        $externalId = hash('sha256', $externalId);

        $published = $this->firstText($node, ['pubDate', 'published', 'updated', 'date']);
        $publishedAt = $this->parseDate($published);

        return [
            'external_id' => $externalId,
            'source_name' => $source['name'],
            'title' => $title,
            'link' => $link,
            'summary' => $summary,
            'content' => $content !== '' ? $content : $summary,
            'thumbnail' => $this->thumbnail($node),
            'language' => $source['language'] ?? 'fr',
            'publication_date' => $publishedAt,
            'keywords' => $this->keywords($node, $title, $summary),
        ];
    }

    /**
     * Reads a tag by local name, transparently handling the various namespace
     * prefixes these feeds use.
     *
     * @param  array<int, string>  $names
     */
    private function firstText(SimpleXMLElement $node, array $names): string
    {
        $candidates = $node->children();

        foreach ($names as $name) {
            foreach ($candidates as $child) {
                if ($child->getName() === $name && trim((string) $child) !== '') {
                    return (string) $child;
                }
            }
        }

        // Fall back to the explicitly declared namespaces.
        $prefixed = [
            'content:encoded' => [self::NS_CONTENT, 'encoded'],
            'dc:creator' => [self::NS_DC, 'creator'],
        ];

        foreach ($names as $name) {
            if (! isset($prefixed[$name])) {
                continue;
            }
            [$uri, $local] = $prefixed[$name];
            $value = trim((string) $node->children($uri)->{$local});
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }

    private function firstLink(SimpleXMLElement $node): string
    {
        $plain = trim((string) $node->link);

        if ($plain !== '' && ! str_starts_with($plain, '<')) {
            return $plain;
        }

        // Atom puts the URL in <link href="..."/>; RSS may wrap it in CDATA.
        foreach ($node->link as $link) {
            $attrs = $link->attributes();
            $href = (string) ($attrs['href'] ?? '');

            if ($href !== '') {
                return $href;
            }

            $value = trim((string) $link);
            if ($value !== '') {
                return $value;
            }
        }

        $guid = $this->clean($this->firstText($node, ['guid', 'id']));

        return str_starts_with($guid, 'http') ? $guid : '';
    }

    private function thumbnail(SimpleXMLElement $node): ?string
    {
        foreach ($node->children(self::NS_MEDIA)->content as $media) {
            $attrs = $media->attributes();
            $url = trim((string) ($attrs['url'] ?? ''));

            if ($url !== '' && preg_match('#^https?://#i', $url)) {
                return $url;
            }
        }

        foreach ($node->children(self::NS_MEDIA)->thumbnail as $thumb) {
            $url = trim((string) $thumb->attributes()['url'] ?? '');

            if ($url !== '' && preg_match('#^https?://#i', $url)) {
                return $url;
            }
        }

        foreach ($node->enclosure as $enclosure) {
            $type = (string) $enclosure->attributes()['type'] ?? '';
            $url = trim((string) $enclosure->attributes()['url'] ?? '');

            if ($url !== '' && str_starts_with($type, 'image/')) {
                return $url;
            }
        }

        return null;
    }

    /**
     * @return array<int, string>
     */
    private function keywords(SimpleXMLElement $node, string $title, string $summary): array
    {
        $keywords = [];

        foreach ($node->category as $category) {
            $value = $this->clean((string) $category);
            $label = (string) $category->attributes()['term'] ?? '';

            foreach ([$value, $label] as $candidate) {
                if ($candidate !== '') {
                    $keywords[] = Str::lower($this->clean($candidate));
                }
            }
        }

        $author = $this->clean($this->firstText($node, ['dc:creator', 'author', 'name']));
        if ($author !== '') {
            $keywords[] = Str::lower($author);
        }

        return array_values(array_unique(array_merge(
            $keywords,
            [Str::lower($title), Str::lower($summary)]
        )));
    }

    private function parseDate(string $raw): string
    {
        $raw = trim($raw);

        if ($raw !== '') {
            $ts = strtotime($raw);
            if ($ts !== false) {
                // Some publishers (Leral ships every item stamped +1 day) date
                // their feed ahead of the clock. Left alone, those rows outrank
                // every real article and permanently monopolise the homepage.
                // Roll the date back whole days until it is in the past; this
                // keeps the feed's own relative spacing instead of flattening
                // every item onto "now".
                $shifted = 0;
                while ($ts > time() + self::FUTURE_TOLERANCE && $shifted < 3) {
                    $ts -= 86400;
                    $shifted++;
                }

                return date('Y-m-d H:i:s', $ts);
            }
        }

        return now()->toDateTimeString();
    }

    private function clean(string $html): string
    {
        $text = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\x{00A0}/u', ' ', $text) ?? $text;
        $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;

        return trim(preg_replace('/\n{3,}/', "\n\n", $text) ?? $text);
    }
}
