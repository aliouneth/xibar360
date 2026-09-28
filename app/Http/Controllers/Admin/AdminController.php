<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Ad;
use App\Models\User;
use App\Models\Category;
use App\Models\Ad;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Requests\ArticleRequest;
use App\Http\Requests\AdRequest;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index', [
            'totalArticles' => Article::count(),
            'publishedArticles' => Article::where('is_published', true)->count(),
            'importedArticles' => Article::where('source_type', 'imported')->count(),
            'localArticles' => Article::where('source_type', 'local')->count(),
            'totalUsers' => User::count(),
            'totalCategories' => Category::count(),
            'totalAds' => Ad::where('is_active', true)->count(),
            'usersByRole' => User::withCount('roles')->get(),
        ]);
    }
}
