<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $locale = app()->getLocale();
        $query = $request->input('q');
        $category = $request->input('category');
        $language = $request->input('language');

        $articles = Article::when($query, fn ($q) => $q->search($query))
            ->when($category, fn ($q) => $q->byCategory($category))
            ->when($language, fn ($q) => $q->byLanguage($language))
            ->with('category', 'author')
            ->paginate(15)
            ->appends($request->only('q', 'category', 'language'));

        $categories = \App\Models\Category::active()->get();

        return view('public.search', compact('articles', 'query', 'category', 'language', 'locale', 'categories'));
    }
}
