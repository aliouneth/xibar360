<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['category', 'author'])->where('is_published', true);
        
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title_fr', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('summary_fr', 'like', "%{$search}%")
                  ->orWhere('summary_en', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }
        
        if ($request->has('language')) {
            $query->where('language', $request->language);
        }
        
        $results = $query->latest()->paginate(15)->appends($request->all());
        $categories = Category::where('is_active', true)->get();
        $locale = session('locale', app()->getLocale());

        return view('search.results', compact('results', 'categories', 'locale'));
    }
}
