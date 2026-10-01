@extends('layouts.app')

@section('title', 'Gestion des Articles - Xibar360 Admin')

@section('header_ad')
    <div class="bg-senegal-green py-1">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <span class="text-white font-bold text-xs">🛡️ PANEL D'ADMINISTRATION</span>
        </div>
    </div>
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">📰 Gestion des Articles</h1>
            <p class="text-gray-500 text-sm">Liste de tous les articles publiés et brouillons</p>
        </div>
        <div class="w-full sm:w-auto">
            <a href="{{ route('admin.articles.create') }}" class="bg-senegal-red text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-700 transition-colors inline-block">
                ✨ Nouveau Article
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
        <form method="GET" class="space-y-4 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:space-x-3">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm w-full">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm w-full sm:w-auto">
                <option value="">Toutes catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm w-full sm:w-auto">
                <option value="">Tous statuts</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Publié</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Brouillon</option>
            </select>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <button type="submit" class="bg-senegal-red text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-700 transition-colors w-full sm:w-auto">Filtrer</button>
                <a href="{{ route('admin.articles.index') }}" class="text-gray-500 text-sm hover:text-senegal-red transition-colors text-center px-4 py-2 w-full sm:w-auto">Réinitialiser</a>
            </div>
        </form>
    </div>

    {{-- Articles Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Catégorie</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden md:table-cell">Auteur</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Vues</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($articles as $article)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    @if($article->image)
                                        <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-10 h-10 rounded object-cover mr-3">
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm">{{ Str::limit($article->title_fr ?? $article->title, 50) }}</div>
                                        <div class="text-xs text-gray-500 hidden md:block">{{ \Illuminate\Support\Str::limit(strip_tags($article->content_fr ?? $article->content), 30) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden sm:table-cell">
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $article->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-4 hidden md:table-cell text-sm text-gray-600">{{ $article->user->name ?? '-' }}</td>
                            <td class="px-4 py-4 hidden lg:table-cell text-sm text-gray-500">{{ $article->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-4">
                                <form method="POST" action="{{ route('admin.articles.toggle-publish', $article) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ $article->published ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $article->published ? 'Publié ✓' : 'Brouillon' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-4 py-4 hidden lg:table-cell text-sm text-gray-500">{{ $article->views ?? 0 }}</td>
                            <td class="px-4 py-4 text-right text-sm font-medium">
                                <div class="flex flex-col sm:flex-row items-center justify-end space-y-2 sm:space-y-0 sm:space-x-2">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="text-blue-600 hover:text-blue-900 transition-colors text-center" title="Modifier">✏️</a>
                                    <a href="{{ route('articles.show', $article) }}" target="_blank" class="text-green-600 hover:text-green-900 transition-colors text-center" title="Voir">👁️</a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="inline" onsubmit="return confirm('Supprimer cet article ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors text-center" title="Supprimer">🗑️</a>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-gray-500">
                Affichage de {{ $articles->firstItem() ?? 0 }} à {{ $articles->lastItem() ?? 0 }} sur {{ $articles->total() }} articles
            </p>
            <div class="flex justify-center sm:justify-end">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
@endsection
