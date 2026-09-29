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
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">📰 Gestion des Articles</h1>
            <p class="text-gray-500 text-sm">Liste de tous les articles publiés et brouillons</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.articles.create') }}" class="bg-senegal-red text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-700 transition-colors">
                ✨ Nouveau Article
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-md p-4 mb-6 flex flex-wrap gap-4 items-center">
        <form method="GET" class="flex items-center space-x-3 w-full lg:w-auto">
            <div class="relative">
                <input type="text" name="search" placeholder="Rechercher..." value="{{ request('search') }}" class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm w-full lg:w-64">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm">
                <option value="">Toutes catégories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm">
                <option value="">Tous statuts</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Publié</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Brouillon</option>
            </select>
            <button type="submit" class="bg-senegal-red text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-red-700 transition-colors">Filtrer</button>
            <a href="{{ route('admin.articles.index') }}" class="text-gray-500 text-sm hover:text-senegal-red transition-colors">Réinitialiser</a>
        </form>
    </div>

    {{-- Articles Table --}}
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Titre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Auteur</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Vues</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($articles as $article)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($article->image)
                                        <img src="{{ $article->image }}" alt="{{ $article->title }}" class="w-10 h-10 rounded object-cover mr-3">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center mr-3 text-gray-400 text-xs">📰</div>
                                    @endif
                                    <div>
                                        <div class="font-medium text-gray-900 text-sm">{{ Str::limit($article->title, 50) }}</div>
                                        <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit(strip_tags($article->content_fr), 30) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">{{ $article->category->name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $article->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $article->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.articles.toggle-publish', $article) }}" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="px-3 py-1 rounded-full text-xs font-bold transition-colors {{ $article->published ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                        {{ $article->published ? 'Publié ✓' : 'Brouillon' }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $article->views ?? 0 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('admin.articles.edit', $article) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Modifier">✏️</a>
                                    <a href="{{ route('articles.show', $article) }}" target="_blank" class="text-green-600 hover:text-green-900 transition-colors" title="Voir">👁️</a>
                                    <form method="POST" action="{{ route('admin.articles.destroy', $article) }}" class="inline" onsubmit="return confirm('Supprimer cet article ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Supprimer">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">
                Affichage de {{ $articles->firstItem() ?? 0 }} à {{ $articles->lastItem() ?? 0 }} sur {{ $articles->total() }} articles
            </p>
            <div class="flex space-x-1">
                {{ $articles->links() }}
            </div>
        </div>
    </div>
@endsection
