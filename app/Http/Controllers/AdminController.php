<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Ad;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $totalArticles = Article::count();
        $publishedArticles = Article::where('is_published', true)->count();
        $importedArticles = Article::imported()->count();
        $localArticles = Article::local()->count();
        $totalUsers = User::count();
        $totalAdmins = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count();
        $totalEditors = User::whereHas('roles', fn ($q) => $q->where('name', 'editor'))->count();
        $activeAds = Ad::active()->count();
        $totalCategories = Category::count();
        $activeCategories = Category::where('is_active', true)->count();

        return view('admin.dashboard', compact(
            'totalArticles',
            'publishedArticles',
            'importedArticles',
            'localArticles',
            'totalUsers',
            'totalAdmins',
            'totalEditors',
            'activeAds',
            'totalCategories',
            'activeCategories'
        ));
    }

    public function articles()
    {
        $this->authorize('viewAny', Article::class);

        $articles = Article::with('category', 'author')
            ->when(request('imported'), fn ($q) => $q->imported())
            ->when(request('local'), fn ($q) => $q->local())
            ->when(request('category'), fn ($q) => $q->byCategory(request('category')))
            ->when(request('language'), fn ($q) => $q->byLanguage(request('language')))
            ->when(request('published'), fn ($q) => $q->where('is_published', request('published') === 'true'))
            ->latest()
            ->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    public function categories()
    {
        $this->authorize('viewAny', Category::class);

        $categories = Category::latest()->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function ads()
    {
        $this->authorize('viewAny', Ad::class);

        $ads = Ad::latest()->paginate(20);

        return view('admin.ads.index', compact('ads'));
    }

    public function users()
    {
        $this->authorize('viewAny', User::class);

        $users = User::with('roles')->latest()->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function settings()
    {
        $this->authorize('viewAny', Setting::class);

        return view('admin.settings.index');
    }
}
