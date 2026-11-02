<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Ad;
use App\Models\PageView;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', array_merge([
            'totalArticles' => Article::count(),
            'publishedArticles' => Article::where('is_published', true)->count(),
            'importedArticles' => Article::where('source_type', 'imported')->count(),
            'localArticles' => Article::where('source_type', 'local')->count(),
            'totalUsers' => \App\Models\User::count(),
            'totalAdmins' => User::whereHas('roles', fn ($q) => $q->where('slug', 'admin'))->count(),
            'totalEditors' => User::whereHas('roles', fn ($q) => $q->where('slug', 'editor'))->count(),
            'totalCategories' => \App\Models\Category::count(),
            'activeAds' => Ad::where('is_active', true)->count(),
            'usersByRole' => \App\Models\User::withCount('roles')->get(),
            'articlesByCategory' => Article::select('category_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('category_id')->get(),
            'lastRefresh' => Cache::get('news-last-refresh'),
        ], $this->visitorCounts()));
    }

    /**
     * Unique visitors (distinct visitor_hash) over the dashboard windows.
     * Same definition as StatsController so both pages always agree.
     *
     * @return array<string, int>
     */
    private function visitorCounts(): array
    {
        $count = fn (Carbon $since): int => PageView::where('created_at', '>=', $since)
            ->distinct()
            ->count('visitor_hash');

        return [
            'visitorsAllTime' => PageView::distinct()->count('visitor_hash'),
            'visitorsToday' => $count(now()->startOfDay()),
            'visitors7Days' => $count(now()->subDays(6)->startOfDay()),
            'visitors15Days' => $count(now()->subDays(14)->startOfDay()),
            'visitors30Days' => $count(now()->subDays(29)->startOfDay()),
        ];
    }
}
