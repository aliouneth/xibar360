<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|editor']);
        $this->middleware('permission:edit-articles')->only(['edit', 'update', 'togglePublish']);
    }

    public function index()
    {
        $this->authorize('viewAny', Article::class);

        $articles = Article::with('category', 'author')
            ->when(request('imported'), fn ($q) => $q->where('source_type', 'imported'))
            ->when(request('local'), fn ($q) => $q->where('source_type', 'local'))
            ->when(request('category'), fn ($q) => $q->byCategory(request('category')))
            ->when(request('language'), fn ($q) => $q->byLanguage(request('language')))
            ->when(request('published'), fn ($q) => $q->where('is_published', request('published') === 'true'))
            ->latest()
            ->paginate(20);

        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $this->authorize('create', Article::class);

        $categories = \App\Models\Category::active()->get();

        return view('admin.articles.create', compact('categories'));
    }

    public function store(ArticleRequest $request)
    {
        $this->authorize('create', Article::class);

        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', __('Article created successfully.'));
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);

        if ($article->source_type === 'imported' && auth()->user()->isEditor()) {
            abort(403, __('Imported articles are read-only for editors.'));
        }

        $categories = \App\Models\Category::active()->get();

        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $this->authorize('update', $article);

        if ($article->source_type === 'imported' && auth()->user()->isEditor()) {
            abort(403, __('Imported articles are read-only for editors.'));
        }

        $data = $request->validated();

        if ($request->hasFile('thumbnail')) {
            if ($article->thumbnail) {
                \Storage::disk('public')->delete($article->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', __('Article updated successfully.'));
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);

        if ($article->source_type === 'imported') {
            abort(403, __('Imported articles cannot be deleted.'));
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', __('Article deleted successfully.'));
    }

    public function togglePublish(Article $article)
    {
        $this->authorize('update', $article);

        if ($article->source_type === 'imported' && auth()->user()->isEditor()) {
            abort(403, __('Imported articles are read-only for editors.'));
        }

        $article->update(['is_published' => !$article->is_published]);

        return back()->with('success', __('Article publish status toggled.'));
    }
}
