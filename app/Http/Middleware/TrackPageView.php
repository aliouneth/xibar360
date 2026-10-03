<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    /**
     * Paths that must never inflate the public audience numbers: the back
     * office, the login flow, and the health check.
     */
    private const IGNORED_PREFIXES = [
        'admin',
        'editor',
        'login',
        'register',
        'logout',
        'up',
    ];

    private const BOT_PATTERN = '/bot|crawl|spider|slurp|curl\/|wget|python-requests|headlesschrome|facebookexternalhit|whatsapp|telegram|lighthouse|monitoring|uptime/i';

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only successful GETs count; a 404 is not an audience.
        if ($this->shouldTrack($request, $response)) {
            $this->record($request);
        }

        return $response;
    }

    private function shouldTrack(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($response->getStatusCode() >= 400) {
            return false;
        }

        $path = trim($request->path(), '/');

        if ($path === '' || $path === false) {
            // The front page is real traffic, tracked under "/".
            $path = '/';
        } else {
            $first = explode('/', $path)[0];
            if (in_array($first, self::IGNORED_PREFIXES, true)) {
                return false;
            }
        }

        if (preg_match(self::BOT_PATTERN, (string) $request->userAgent())) {
            return false;
        }

        return true;
    }

    private function record(Request $request): void
    {
        $refererHost = null;
        if ($referer = $request->headers->get('referer')) {
            $refererHost = parse_url($referer, PHP_URL_HOST) ?: null;
        }

        $article = $request->route('article');

        // GeoIP lookup
        $country = $this->lookupCountry($request);
        $region = $this->lookupRegion($request->ip(), $country);

        try {
            PageView::create([
                'article_id' => $article instanceof \App\Models\Article ? $article->id : null,
                'path' => mb_substr($request->path() === '/' ? '/' : '/'.trim($request->path(), '/'), 0, 255),
                'visitor_hash' => $this->visitorHash($request),
                'user_id' => $request->user()?->id,
                'referer_host' => $refererHost ? mb_substr($refererHost, 0, 191) : null,
                'device' => $this->device($request),
                'country' => $country,
                'region' => $region,
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Lookup country code from IP
     */
    private function lookupCountry(Request $request): ?string
    {
        $ip = $request->ip();

        // 1. Check Cloudflare header (available when behind Cloudflare)
        $cfCountry = $request->header('cf-ipcountry');
        if ($cfCountry) {
            return strtoupper($cfCountry);
        }

        // 2. Try local MaxMind database if available and geoip2 package is installed
        $dbPath = config('geoip.database_path', storage_path('app/geoip/GeoLite2-Country.mmdb'));
        if (file_exists($dbPath) && class_exists(\GeoIp2\Database\Reader::class)) {
            try {
                $reader = new \GeoIp2\Database\Reader($dbPath);
                $record = $reader->country($request->ip());
                return $record->country->isoCode;
            } catch (\Throwable $e) {
                // Fall through
            }
        }

        return null;
    }

    private function lookupRegion(string $ip, ?string $country): ?string
    {
        if (!$country) {
            return null;
        }

        $dbPath = config('geoip.city_database_path', storage_path('app/geoip/GeoLite2-City.mmdb'));
        if (file_exists($dbPath) && class_exists(\GeoIp2\Database\Reader::class)) {
            try {
                $reader = new \GeoIp2\Database\Reader($dbPath);
                $record = $reader->city($ip);
                return $record->mostSpecificSubdivision->isoCode ?? $record->country->isoCode;
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        return null;
    }

    private function isPrivateIp(string $ip): bool
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $long = ip2long($ip);
            return ($long >= ip2long('10.0.0.0') && $long <= ip2long('10.255.255.255')) ||
                   ($long >= ip2long('172.16.0.0') && $long <= ip2long('172.31.255.255')) ||
                   ($long >= ip2long('192.168.0.0') && $long <= ip2long('192.168.255.255')) ||
                   ($long >= ip2long('127.0.0.0') && $long <= ip2long('127.255.255.255'));
        }
        return false;
    }

    /**
     * Distinct visitors are counted from this digest. It is deliberately a
     * one-way hash of IP + user agent: no raw IP address is ever persisted.
     */
    private function visitorHash(Request $request): string
    {
        return hash('sha256', $request->ip().'|'.($request->userAgent() ?? ''));
    }

    private function device(Request $request): string
    {
        $ua = (string) $request->userAgent();

        return match (true) {
            (bool) preg_match('/ipad|tablet|playbook|silk/i', $ua) => 'tablet',
            (bool) preg_match('/mobile|iphone|android/i', $ua) => 'mobile',
            default => 'desktop',
        };
    }
}
