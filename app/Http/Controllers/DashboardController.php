<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Ad;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $stats = [
            'total_articles' => Article::count(),
            'published_articles' => Article::where('is_published', true)->count(),
            'drafts' => Article::where('is_published', false)->count(),
            'imported_articles' => Article::imported()->count(),
            'local_articles' => Article::local()->count(),
            'total_users' => User::count(),
            'total_admins' => User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count(),
            'total_editors' => User::whereHas('roles', fn ($q) => $q->where('name', 'editor'))->count(),
            'active_ads' => Ad::active()->count(),
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'total_ad_inactive' => Ad::where('is_active', false)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
