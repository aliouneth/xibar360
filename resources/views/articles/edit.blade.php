@extends('layouts.app')

@section('title', 'Modifier l\'Article - SunuNews Admin')

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
            <h1 class="text-2xl font-bold text-gray-900">✏️ Modifier l'Article</h1>
            <p class="text-gray-500 text-sm">{{ $article->title }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.articles.index') }}" class="text-gray-600 hover:text-senegal-red transition-colors text-sm font-medium">← Retour</a>
            <a href="{{ route('articles.show', $article) }}" target="_blank" class="bg-gray-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-gray-700 transition-colors">Voir en ligne</a>
        </div>
    </div>

    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                {{-- Title --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center">📝 Titre</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $article->title) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent">
                            @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Résumé</label>
                            <textarea name="summary" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent">{{ old('summary', $article->summary) }}</textarea>
                            @error('summary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Corps de l'article</label>
                            <textarea name="body" rows="10" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent font-mono text-sm">{{ old('body', $article->body) }}</textarea>
                            @error('body')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Category & Image --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center">📂 Catégorie & Image</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                            <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red">
                            <p class="text-xs text-gray-500 mt-1">Image actuelle: {{ $article->image ? basename($article->image) : 'Aucune' }}</p>
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source URL</label>
                        <input type="url" name="source_url" value="{{ old('source_url', $article->source_url) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent">
                        @error('source_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center">⚙️ Options</h3>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="published" value="1" {{ old('published', $article->published) ? 'checked' : '' }} class="rounded border-gray-300 text-senegal-red focus:ring-senegal-red mr-2">
                            <span class="text-sm font-medium">Publié</span>
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de publication</label>
                            <input type="datetime-local" name="published_at" value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red">
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full bg-senegal-red text-white px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors">
                    💾 Mettre à jour
                </button>
            </div>
        </div>
    </form>
@endsection
