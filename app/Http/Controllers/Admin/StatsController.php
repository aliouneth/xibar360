<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use App\Models\Article;
use App\Models\Category;
use App\Models\PageView;
use App\Models\Role;
use App\Models\User;
use App\Services\WebScannerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    /** Days shown in the publication trend. */
    private const TREND_DAYS = 14;

    public function index(Request $request)
    {
        $days = min(90, max(7, (int) $request->integer('days', self::TREND_DAYS)));

        $visitors = $this->visitorStats($days);

        $articles = Article::query();
        $published = (clone $articles)->where('is_published', true)->count();
        $drafts = (clone $articles)->where('is_published', false)->count();
        $featured = (clone $articles)->where('is_featured', true)->count();
        $imported = (clone $articles)->where('source_type', 'imported')->count();

        // Publication trend, keyed by calendar date so days with no articles
        // still appear as a zero rather than silently disappearing.
        $since = now()->subDays($days - 1)->startOfDay();

        $rawTrend = Article::where('publication_date', '>=', $since)
            ->select(DB::raw('DATE(publication_date) as day'), DB::raw('COUNT(*) as total'), DB::raw('SUM(source_type = "imported") as imported'))
            ->groupBy('day')
            ->pluck('total', 'day');

        $importedTrend = Article::where('publication_date', '>=', $since)
            ->select(DB::raw('DATE(publication_date) as day'), DB::raw('COUNT(*) as imported'))
            ->where('source_type', 'imported')
            ->groupBy('day')
            ->pluck('imported', 'day');

        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $trend[] = [
                'day' => $date,
                'label' => now()->subDays($i)->translatedFormat('d/m'),
                'total' => (int) ($rawTrend[$date] ?? 0),
                'imported' => (int) ($importedTrend[$date] ?? 0),
            ];
        }

        $maxTrend = max(1, (int) collect($trend)->max('total'));

        foreach ($trend as $i => $point) {
            $trend[$i]['pct'] = $point['total'] > 0
                ? max(2, (int) round($point['total'] / $maxTrend * 100))
                : 2;
            $trend[$i]['importedPct'] = $point['total'] > 0
                ? (int) round($point['imported'] / $point['total'] * 100)
                : 0;
        }

        $byCategory = Category::query()
            ->withCount('articles')
            ->orderByDesc('articles_count')
            ->get()
            ->map(fn (Category $c) => [
                'name' => $c->name_fr,
                'total' => $c->articles_count,
                'published' => Article::where('category_id', $c->id)->where('is_published', true)->count(),
            ]);

        $bySource = Article::whereNotNull('source_name')
            ->select('source_name', DB::raw('COUNT(*) as total'), DB::raw('MAX(publication_date) as last_seen'))
            ->groupBy('source_name')
            ->orderByDesc('total')
            ->get()
            ->map(fn ($r) => [
                'name' => $r->source_name,
                'total' => (int) $r->total,
                'last_seen' => $r->last_seen,
            ]);

        $byLanguage = Article::select('language', DB::raw('COUNT(*) as total'))
            ->groupBy('language')
            ->pluck('total', 'language');

        $byRole = Role::query()
            ->withCount('users')
            ->orderByDesc('users_count')
            ->get()
            ->map(fn (Role $r) => ['name' => $r->name, 'total' => $r->users_count]);

        $topAuthors = User::query()
            ->withCount(['articles' => fn ($q) => $q->where('source_type', 'local')])
            ->orderByDesc('articles_count')
            ->orderBy('name')
            ->take(5)
            ->get()
            ->map(fn (User $u) => ['name' => $u->name, 'total' => $u->articles_count]);

        $ads = Ad::select('zone', DB::raw('COUNT(*) as total'), DB::raw('SUM(is_active) as active'))
            ->groupBy('zone')
            ->get()
            ->map(fn ($r) => [
                'zone' => $r->zone ?: 'other',
                'total' => (int) $r->total,
                'active' => (int) $r->active,
            ]);

        // Feeds the admin can act on: configured vs actually producing rows.
        $sourceHealth = collect(config('news_sources.sources', []))
            ->map(function (array $source) use ($bySource) {
                $match = $bySource->firstWhere('name', $source['name']);
                $lastRefresh = Cache::get('news-last-refresh');

                return [
                    'name' => $source['name'],
                    'enabled' => (bool) ($source['enabled'] ?? true),
                    'stored' => $match['total'] ?? 0,
                    'last_seen' => $match['last_seen'] ?? null,
                ];
            })
            ->sortByDesc('stored')
            ->values();

        return view('admin.stats.index', [
            'days' => $days,
            'hasVisitorData' => $visitors['hasData'],
            'visitorSince' => $visitors['since']->translatedFormat('d/m/Y H:i'),
            'visitorCards' => $this->visitorCards($visitors),
            'visitorTrend' => $visitors['trend'],
            'topViewedArticles' => $visitors['topArticles'],
            'topReferrers' => $visitors['topReferrers'],
            'deviceSplit' => $visitors['devices'],
            'topPaths' => $visitors['topPaths'],
            'totalCards' => [
                [
                    'label' => __('Total Articles'),
                    'value' => number_format($published + $drafts),
                    'sub' => $published.' '.__('published'),
                ],
                [
                    'label' => __('Imported'),
                    'value' => number_format($imported),
                    'sub' => ($published + $drafts - $imported).' '.__('local'),
                ],
                [
                    'label' => __('Drafts'),
                    'value' => number_format($drafts),
                    'sub' => $featured.' '.__('featured'),
                ],
                [
                    'label' => __('Users'),
                    'value' => number_format(User::count()),
                    'sub' => $byRole->pluck('total')->sum().' '.__('with a role'),
                ],
                [
                    'label' => __('Categories'),
                    'value' => number_format(Category::count()),
                    'sub' => $byLanguage->keys()->count().' '.__('languages'),
                ],
                [
                    'label' => __('Ads'),
                    'value' => number_format(Ad::count()),
                    'sub' => Ad::where('is_active', true)->count().' '.__('active'),
                ],
            ],
            'totals' => [
                'articles' => $published + $drafts,
                'published' => $published,
                'drafts' => $drafts,
                'featured' => $featured,
                'imported' => $imported,
                'local' => ($published + $drafts) - $imported,
                'users' => User::count(),
                'categories' => Category::count(),
                'ads' => Ad::count(),
                'adsActive' => Ad::where('is_active', true)->count(),
                'languages' => $byLanguage->keys()->count(),
            ],
            'trend' => $trend,
            'byCategory' => $byCategory,
            'bySource' => $bySource,
            'byLanguage' => $byLanguage,
            'byRole' => $byRole,
            'topAuthors' => $topAuthors,
            'ads' => $ads,
            'sourceHealth' => $sourceHealth,
            'lastRefresh' => Cache::get('news-last-refresh'),
        ]);
    }

    /**
     * Label/value/subtitle triples for the visitor summary strip. Built here
     * rather than inline in Blade so the view carries no formatting logic.
     */
    private function visitorCards(array $visitors): array
    {
        return [
            [
                'label' => __('Page views'),
                'value' => number_format($visitors['windowViews']),
                'sub' => number_format($visitors['totalViews']).' '.__('all time'),
            ],
            [
                'label' => __('Unique visitors'),
                'value' => number_format($visitors['windowVisitors']),
                'sub' => number_format($visitors['totalVisitors']).' '.__('all time'),
            ],
            [
                'label' => __('Today'),
                'value' => number_format($visitors['todayViews']),
                'sub' => $visitors['todayVisitors'].' '.__('visitors'),
            ],
            [
                'label' => __('Article views'),
                'value' => number_format($visitors['articleViews']),
                'sub' => __('of total views'),
            ],
            [
                'label' => __('Views per visitor'),
                'value' => $visitors['avgPerVisitor'],
                'sub' => __('all time avg'),
            ],
        ];
    }

    /**
     * Public audience figures, derived from page_views.
     *
     * A "visitor" is a distinct visitor_hash, which is a one-way digest of IP
     * + user agent. Nothing here counts an admin session, a login request or
     * a crawler, because TrackPageView never writes those.
     */
    private function visitorStats(int $days): array
    {
        $since = now()->subDays($days - 1)->startOfDay();
        $views = PageView::query();

        $daily = PageView::where('created_at', '>=', $since)
            ->select(DB::raw('DATE(created_at) as day'), DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_hash) as visitors'))
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $trend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $row = $daily->get($date);

            $trend[] = [
                'day' => $date,
                'label' => now()->subDays($i)->translatedFormat('d/m'),
                'views' => (int) ($row->views ?? 0),
                'visitors' => (int) ($row->visitors ?? 0),
            ];
        }

        $maxTrendViews = max(1, (int) collect($trend)->max('views'));

        foreach ($trend as $i => $point) {
            $trend[$i]['height'] = max(2, (int) round($point['views'] / $maxTrendViews * 100));
        }

        $today = $daily->get(now()->toDateString());

        $topArticles = PageView::query()
            ->whereNotNull('article_id')
            ->where('created_at', '>=', $since)
            ->select('article_id', DB::raw('COUNT(*) as views'), DB::raw('COUNT(DISTINCT visitor_hash) as visitors'))
            ->groupBy('article_id')
            ->orderByDesc('views')
            ->take(10)
            ->get()
            ->map(function ($row) {
                $article = Article::find($row->article_id);

                return [
                    'title' => $article?->title_fr ?? __('(deleted article)'),
                    'url' => $article ? route('articles.show', $article) : '#',
                    'views' => (int) $row->views,
                    'visitors' => (int) $row->visitors,
                    'source' => $article?->source_name,
                ];
            });

        $topReferrers = PageView::where('created_at', '>=', $since)
            ->whereNotNull('referer_host')
            ->select('referer_host', DB::raw('COUNT(*) as views'))
            ->groupBy('referer_host')
            ->orderByDesc('views')
            ->take(8)
            ->get()
            ->map(fn ($r) => ['host' => $r->referer_host, 'views' => (int) $r->views]);

        $deviceCounts = PageView::where('created_at', '>=', $since)
            ->select('device', DB::raw('COUNT(*) as views'))
            ->groupBy('device')
            ->orderByDesc('views')
            ->get();

        $deviceTotal = max(1, (int) $deviceCounts->sum('views'));

        $devices = $deviceCounts->map(fn ($r) => [
            'device' => $r->device ?: 'other',
            'views' => (int) $r->views,
            'pct' => (int) round((int) $r->views / $deviceTotal * 100),
        ]);

        $topPaths = PageView::where('created_at', '>=', $since)
            ->whereNull('article_id')
            ->select('path', DB::raw('COUNT(*) as views'))
            ->groupBy('path')
            ->orderByDesc('views')
            ->take(8)
            ->get()
            ->map(fn ($r) => ['path' => $r->path, 'views' => (int) $r->views]);

        return [
            'totalViews' => (clone $views)->count(),
            'totalVisitors' => (clone $views)->distinct()->count('visitor_hash'),
            'todayViews' => (int) ($today->views ?? 0),
            'todayVisitors' => (int) ($today->visitors ?? 0),
            'windowViews' => (clone $views)->where('created_at', '>=', $since)->count(),
            'windowVisitors' => (clone $views)->where('created_at', '>=', $since)->distinct()->count('visitor_hash'),
            'articleViews' => (clone $views)->whereNotNull('article_id')->count(),
            'avgPerVisitor' => (function () use ($views) {
                $visitors = (clone $views)->distinct()->count('visitor_hash');

                return $visitors > 0 ? round((clone $views)->count() / $visitors, 2) : 0;
            })(),
            'trend' => $trend,
            'topArticles' => $topArticles,
            'topReferrers' => $topReferrers,
            'devices' => $devices,
            'topPaths' => $topPaths,
            'hasData' => PageView::exists(),
            'since' => $since,
        ];
    }
}
