<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NewsImporterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewsController extends Controller
{
    private const LOCK_KEY = 'news-refresh-lock';

    private const LOCK_SECONDS = 300;

    public function __construct(private readonly NewsImporterService $importer) {}

    public function refresh(): RedirectResponse
    {
        // A refresh talks to ten external feeds and can take ~20s, which is
        // well past the default web timeout.
        set_time_limit((int) config('news_sources.timeout', 20) * count(config('news_sources.sources', [])) + 60);

        // Guard against a double-click running two concurrent imports.
        $lock = Cache::lock(self::LOCK_KEY, self::LOCK_SECONDS);

        if (! $lock->get()) {
            return back()->with('error', 'Un rafraîchissement est déjà en cours. Veuillez patienter.');
        }

        try {
            $report = $this->importer->import();

            Cache::put('news-last-refresh', [
                'at' => now()->toDateTimeString(),
                'imported' => $report['imported'],
                'skipped' => $report['skipped'],
            ], now()->addDays(30));

            return back()->with('success', sprintf(
                'Actualités rafraîchies : %d nouvel article, %d déjà connu.',
                $report['imported'],
                $report['skipped']
            ))->with('news_report', $report);
        } catch (Throwable $e) {
            Log::error('news refresh failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Échec du rafraîchissement : '.$e->getMessage());
        } finally {
            $lock->release();
        }
    }
}
