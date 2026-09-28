<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Pulls upstream feeds and persists genuinely new articles.
 *
 * Re-running is safe: every incoming item is keyed on the feed's stable
 * external id, so a refresh only inserts what is not already stored.
 */
class NewsImporterService
{
    public function __construct(private readonly WebScannerService $scanner) {}

    /**
     * @return array{
     *     imported:int, skipped:int, total:int, sources:array<int,array<string,mixed>>,
     *     failures:array<int,array<string,string>>, duration:float
     * }
     */
    public function import(): array
    {
        $started = microtime(true);

        $report = $this->scanner->scanWithReport();

        $imported = 0;
        $skipped = 0;
        $failures = [];

        $author = $this->resolveAuthor();
        $categories = $this->categoryMap();

        // De-duplicate within the batch first, then against what is stored.
        $incoming = [];
        foreach ($report['articles'] as $article) {
            $incoming[$article['external_id']] = $article;
        }

        $existing = Article::whereIn('external_id', array_keys($incoming))
            ->pluck('external_id')
            ->all();

        $existing = array_flip($existing);

        $toInsert = [];
        foreach ($incoming as $externalId => $article) {
            if (isset($existing[$externalId])) {
                $skipped++;

                continue;
            }
            $toInsert[] = $this->toRow($article, $author, $categories);
        }

        // Insert in chunks inside one transaction: a mid-way failure must not
        // leave a partially imported refresh behind.
        $imported = 0;
        $rowFailures = 0;

        foreach (array_chunk($toInsert, 50) as $chunk) {
            try {
                DB::transaction(function () use ($chunk, &$imported, &$rowFailures) {
                    foreach ($chunk as $row) {
                        try {
                            Article::create($row);
                            $imported++;
                        } catch (Throwable $e) {
                            // A unique-key collision from two feeds sharing a
                            // guid must not abort the whole refresh.
                            Log::warning('news import: row skipped', [
                                'external_id' => $row['external_id'] ?? null,
                                'error' => $e->getMessage(),
                            ]);
                            $rowFailures++;
                        }
                    }
                });
            } catch (Throwable $e) {
                Log::error('news import: chunk failed', ['error' => $e->getMessage()]);
            }
        }

        foreach ($report['sources'] as $source) {
            if ($source['ok'] === false) {
                $failures[] = ['name' => $source['name'], 'error' => $source['error']];
            }
        }

        $result = [
            'imported' => $imported,
            'skipped' => $skipped,
            'failed_rows' => $rowFailures,
            'total' => count($incoming),
            'sources' => $report['sources'],
            'failures' => $failures,
            'duration' => round(microtime(true) - $started, 2),
        ];

        Log::info('news import finished', $result);

        return $result;
    }

    private function resolveAuthor(): User
    {
        $email = config('news_sources.author_email');

        $user = User::where('email', $email)->first()
            ?? User::orderBy('id')->first();

        if (! $user) {
            throw new \RuntimeException(
                'No user available to own imported articles (articles.user_id is NOT NULL). '
                ."Create a user or set NEWS_AUTHOR_EMAIL. Looked for [{$email}]."
            );
        }

        return $user;
    }

    /**
     * @return array<string, int> name_fr => id
     */
    private function categoryMap(): array
    {
        return Category::pluck('id', 'name_fr')->all();
    }

    /**
     * @param  array<string, mixed>  $article
     * @param  array<string, int>  $categories
     * @return array<string, mixed>
     */
    private function toRow(array $article, User $author, array $categories): array
    {
        $title = $article['title'];
        $summary = $article['summary'] !== '' ? $article['summary'] : $article['content'];
        $content = $article['content'] !== '' ? $article['content'] : $summary;

        // The articles table requires both language variants to be NOT NULL.
        // Upstream feeds are monolingual, so both columns get the original
        // text; the `language` column records which side it really is.
        return [
            'external_id' => $article['external_id'],
            'source_name' => $article['source_name'],
            'title_fr' => $title,
            'title_en' => $title,
            'summary_fr' => $summary,
            'summary_en' => $summary,
            'content_fr' => $content,
            'content_en' => $content,
            'category_id' => $this->guessCategory($article, $categories),
            'source_url' => $article['link'],
            'thumbnail' => $article['thumbnail'],
            'publication_date' => $article['publication_date'],
            'language' => $article['language'] ?? 'fr',
            'source_type' => 'imported',
            'is_published' => true,
            'is_featured' => false,
            'user_id' => $author->id,
        ];
    }

    /**
     * Scores the article's keywords against the configured category keyword
     * lists. Falls back to the first active category.
     *
     * @param  array<string, mixed>  $article
     * @param  array<string, int>  $categories
     */
    private function guessCategory(array $article, array $categories): ?int
    {
        $lists = config('news_sources.category_keywords', []);
        $haystack = implode(' ', $article['keywords'] ?? []);
        $haystack = Str::lower($haystack);

        $bestId = null;
        $bestScore = 0;

        foreach ($lists as $name => $words) {
            if (! isset($categories[$name])) {
                continue;
            }

            $score = 0;
            foreach ($words as $word) {
                if (Str::contains($haystack, Str::lower($word))) {
                    $score++;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestId = $categories[$name];
            }
        }

        if ($bestId !== null) {
            return $bestId;
        }

        $fallback = Category::where('is_active', true)->orderBy('id')->value('id');

        return $fallback ?: ($categories[array_key_first($categories)] ?? null);
    }
}
