<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index(Request $request)
    {
        $locale = session('locale', app()->getLocale());

        $featured = $this->sourceDiverse(
            Article::with('category')
                ->where('is_published', true)
                ->where('is_featured', true)
                ->whereNotNull('thumbnail')
                ->where('thumbnail', '!=', '')
        )->take(5)->get();

        // With no article explicitly featured the hero would disappear. Fall
        // back to the most recent stories so the front page still leads with
        // real news instead of disappearing or reverting to filler.
        if ($featured->isEmpty()) {
            $featured = $this->sourceDiverse(
                Article::with('category')
                    ->where('is_published', true)
                    ->whereNotNull('thumbnail')
                    ->where('thumbnail', '!=', '')
            )->take(5)->get();
        }

        // The hero always renders two large cards, so make sure both slots
        // exist even on a nearly empty catalogue. Any story that has no
        // thumbnail of its own falls back to the placeholder image in the view.
        if ($featured->count() < 2) {
            $fill = Article::with('category')
                ->where('is_published', true)
                ->whereNotIn('id', $featured->pluck('id')->all())
                ->orderByRaw("CASE WHEN thumbnail IS NULL OR thumbnail = '' THEN 1 ELSE 0 END")
                ->orderByDesc('publication_date')
                ->orderByDesc('id')
                ->take(2 - $featured->count())
                ->get();

            $featured = $featured->concat($fill);
        }

        $featuredIds = $featured->pluck('id')->toArray();

        $categories = Category::where('is_active', true)->get();

        // Each block runs its own query. Filtering one shared paginator in the
        // view starved most blocks, so freshly imported stories never showed.
        $sections = $categories->mapWithKeys(fn (Category $category) => [
            $category->name_fr => $this->sourceDiverse(
                Article::with('category')
                    ->where('is_published', true)
                    ->where('category_id', $category->id)
            )->take(3)->get(),
        ]);

        $localPicks = Article::with('category')
            ->where('is_published', true)->where('source_type', 'local')
            ->orderByDesc('publication_date')->orderByDesc('id')
            ->take(3)->get();

        // Newest by publication date, not import time, so a story published
        // earlier but picked up now cannot outrank genuinely newer news.
        // Sources are interleaved (round-robin by per-source recency rank) so
        // a single fresh batch from one publisher cannot dominate the feed.
        $latest = $this->sourceDiverse(
            Article::with(['category', 'author'])
                ->where('is_published', true)
                ->whereNotIn('articles.id', $featuredIds)
        )->paginate(48);

        return view('home', compact('featured', 'categories', 'sections', 'localPicks', 'latest', 'locale'));
    }

    /**
     * Orders a query so adjacent rows come from different sources without
     * sacrificing freshness: articles are grouped into ~1 week recency bands
     * (newest band first) and, inside each band, sources are interleaved by
     * their rank. A single fresh batch from one publisher therefore cannot fill
     * the feed, while a stale source cannot jump above genuinely newer news.
     */
    private function sourceDiverse(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $ageBucket = 'FLOOR(DATEDIFF(CURDATE(), COALESCE(articles.publication_date, articles.created_at)) / 7)';

        return $query
            ->select('articles.*')
            ->selectRaw("{$ageBucket} as age_bucket")
            ->selectRaw(
                "ROW_NUMBER() OVER (PARTITION BY articles.source_name, {$ageBucket} ORDER BY articles.publication_date DESC, articles.id DESC) as source_rank"
            )
            ->orderBy('age_bucket')
            ->orderBy('source_rank')
            ->orderByDesc('articles.publication_date')
            ->orderByDesc('articles.id');
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

        return view('articles.show', compact('article', 'related', 'latestArticles', 'locale'));
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
            ->where('is_published', true)->latest()->paginate(48);
        $latestArticles = Article::where('is_published', true)->latest()->take(6)->get();

        return view('public.category', compact('articles', 'category', 'latestArticles', 'locale'));
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
