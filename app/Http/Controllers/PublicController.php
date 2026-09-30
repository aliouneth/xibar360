<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Ad;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $locale = session('locale', app()->getLocale());

        $featured = Article::with('category')
            ->where('is_published', true)->where('is_featured', true)
            ->orderByDesc('publication_date')->orderByDesc('id')
            ->take(5)->get();

        // With no article explicitly featured the hero would disappear. Fall
        // back to the most recent stories so the front page still leads with
        // real news instead of disappearing or reverting to filler.
        if ($featured->isEmpty()) {
            $featured = Article::with('category')
                ->where('is_published', true)
                ->orderByDesc('publication_date')->orderByDesc('id')
                ->take(5)->get();
        }

        $featuredIds = $featured->pluck('id')->toArray();

        $categories = Category::where('is_active', true)->get();
        $headerAds = Ad::where('zone', 'header')->where('is_active', true)->get();
        $sidebarAds = Ad::where('zone', 'sidebar')->where('is_active', true)->get();
        $inlineAds = Ad::where('zone', 'inline')->where('is_active', true)->get();

        // Each block runs its own query. Filtering one shared paginator in the
        // view starved most blocks, so freshly imported stories never showed.
        $sections = $categories->mapWithKeys(fn (Category $category) => [
            $category->name_fr => Article::with('category')
                ->where('is_published', true)
                ->where('category_id', $category->id)
                ->orderByDesc('publication_date')->orderByDesc('id')
                ->take(3)->get(),
        ]);

        $localPicks = Article::with('category')
            ->where('is_published', true)->where('source_type', 'local')
            ->orderByDesc('publication_date')->orderByDesc('id')
            ->take(3)->get();

        // Newest by publication date, not import time, so a story published
        // earlier but picked up now cannot outrank genuinely newer news.
        $latest = Article::with(['category', 'author'])
            ->where('is_published', true)
            ->whereNotIn('id', $featuredIds)
            ->orderByDesc('publication_date')->orderByDesc('id')
            ->paginate(12);

        return view('home', compact('featured', 'categories', 'sections', 'localPicks', 'latest', 'headerAds', 'sidebarAds', 'inlineAds', 'locale'));
    }

    public function show(Article $article)
    {
        $locale = session('locale', app()->getLocale());

        $related = Article::where('category_id', $article->category_id)
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->latest()->take(6)->get();
        
        if ($related->isEmpty()) {
            $related = Article::where('is_published', true)->latest()->take(6)->get();
        }
        
        $latestArticles = Article::where('is_published', true)->latest()->take(6)->get();
        $headerAds = Ad::where('zone', 'header')->where('is_active', true)->get();
        $sidebarAds = Ad::where('zone', 'sidebar')->where('is_active', true)->get();
        
        return view('articles.show', compact('article', 'related', 'latestArticles', 'headerAds', 'sidebarAds', 'locale'));
    }

    public function localizedShow(string $lang, Article $article)
    {
        $this->applyLocale($lang);

        return $this->show($article);
    }

    public function category(Category $category)
    {
        $locale = session('locale', app()->getLocale());
        $articles = Article::where('category_id', $category->id)
            ->where('is_published', true)->latest()->paginate(12);
        $latestArticles = Article::where('is_published', true)->latest()->take(6)->get();
        $headerAds = Ad::where('zone', 'header')->where('is_active', true)->get();
        $sidebarAds = Ad::where('zone', 'sidebar')->where('is_active', true)->get();
        
        return view('public.category', compact('articles', 'category', 'latestArticles', 'headerAds', 'sidebarAds', 'locale'));
    }

    public function localizedCategory(string $lang, Category $category)
    {
        $this->applyLocale($lang);

        return $this->category($category);
    }

    private function applyLocale(string $lang): void
    {
        if (in_array($lang, ['fr', 'en'], true)) {
            session(['locale' => $lang]);
            app()->setLocale($lang);
        }
    }

    public function search(Request $request)
    {
        $locale = session('locale', app()->getLocale());
        $query = Article::where('is_published', true);
        
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
        
        return view('search.results', compact('results', 'categories', 'locale'));
    }
}
