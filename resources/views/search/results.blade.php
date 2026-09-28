@extends('layouts.app')

@section('title', __('Recherche') . ' | SunuNews')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">🔍 {{ __('Recherche') }}</h1>
    @if(request('q'))
        <p class="text-gray-500 text-sm mb-6">Résultats pour « {{ request('q') }} »</p>
    @endif

    <form action="{{ route('search.results') }}" method="GET" class="bg-white rounded-xl shadow-md p-4 mb-6 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label for="q" class="block text-xs font-bold text-gray-500 uppercase mb-1">Mot-clé</label>
            <input type="text" name="q" id="q" value="{{ request('q') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm">
        </div>
        <div>
            <label for="language" class="block text-xs font-bold text-gray-500 uppercase mb-1">Langue</label>
            <select name="language" id="language" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Toutes</option>
                <option value="fr" {{ request('language') === 'fr' ? 'selected' : '' }}>Français</option>
                <option value="en" {{ request('language') === 'en' ? 'selected' : '' }}>English</option>
            </select>
        </div>
        <div>
            <label for="category" class="block text-xs font-bold text-gray-500 uppercase mb-1">Catégorie</label>
            <select name="category" id="category" class="px-4 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Toutes</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ (string) request('category') === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name_fr ?? $cat->name_en }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-senegal-red text-white px-5 py-2 rounded-lg font-bold text-sm hover:bg-red-700 transition-colors">Rechercher</button>
        <a href="{{ route('search.results') }}" class="text-gray-500 text-sm hover:text-senegal-red transition-colors">Réinitialiser</a>
    </form>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        @forelse($results as $article)
            <div class="border-b border-gray-100 last:border-0 p-4 hover:bg-gray-50 transition-colors">
                <a href="{{ route('articles.show', $article) }}" class="block">
                    <span class="inline-block bg-senegal-red text-white px-2 py-0.5 rounded text-xs font-bold mb-2">
                        {{ $article->category?->name_fr ?? 'Actualité' }}
                    </span>
                    <h2 class="font-bold text-gray-900 mb-1 hover:text-senegal-red transition-colors">
                        {{ $article->getTitle($locale) }}
                    </h2>
                    @if($article->getSummary($locale))
                        <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($article->getSummary($locale), 180) }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-2">
                        {{ $article->publication_date?->format('d/m/Y') }}
                    </p>
                </a>
            </div>
        @empty
            <p class="text-gray-500 text-sm p-6 text-center">Aucun article trouvé.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $results->links() }}
    </div>
</div>
@endsection
