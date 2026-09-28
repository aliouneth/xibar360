<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
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

        try {
            PageView::create([
                'article_id' => $article instanceof \App\Models\Article ? $article->id : null,
                'path' => mb_substr($request->path() === '/' ? '/' : '/'.trim($request->path(), '/'), 0, 255),
                'visitor_hash' => $this->visitorHash($request),
                'user_id' => $request->user()?->id,
                'referer_host' => $refererHost ? mb_substr($refererHost, 0, 191) : null,
                'device' => $this->device($request),
            ]);
        } catch (\Throwable $e) {
            // Audience tracking must never break a page render.
            report($e);
        }
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
