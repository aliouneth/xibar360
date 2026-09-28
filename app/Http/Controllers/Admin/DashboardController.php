<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalArticles' => Article::count(),
            'publishedArticles' => Article::where('is_published', true)->count(),
            'importedArticles' => Article::where('source_type', 'imported')->count(),
            'localArticles' => Article::where('source_type', 'local')->count(),
            'totalUsers' => \App\Models\User::count(),
            'totalCategories' => \App\Models\Category::count(),
            'totalAds' => \App\Models\Ad::where('is_active', true)->count(),
            'usersByRole' => \App\Models\User::withCount('roles')->get(),
            'articlesByCategory' => Article::select('category_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
                ->groupBy('category_id')->get(),
            'lastRefresh' => Cache::get('news-last-refresh'),
        ]);
    }
}
