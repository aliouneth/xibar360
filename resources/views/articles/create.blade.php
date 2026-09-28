@extends('layouts.app')

@section('title', 'Créer un Article - SunuNews Admin')

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
            <h1 class="text-2xl font-bold text-gray-900">✨ Créer un nouvel Article</h1>
            <p class="text-gray-500 text-sm">Remplissez le formulaire pour publier un nouvel article</p>
        </div>
        <a href="{{ route('admin.articles.index') }}" class="text-gray-600 hover:text-senegal-red transition-colors text-sm font-medium">← Retour à la liste</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Form --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                {{-- Title --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center">📝 Titre</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titre <span class="text-red-500">*</span></label>
                            <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent" placeholder="Titre de l'article">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent" placeholder="slug-de-l-article">
                            @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Résumé</label>
                            <textarea name="summary" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent" placeholder="Résumé de l'article...">{{ old('summary') }}</textarea>
                            @error('summary')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Corps de l'article <span class="text-red-500">*</span></label>
                            <textarea name="body" rows="10" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent font-mono text-sm" placeholder="Contenu de l'article...">{{ old('body') }}</textarea>
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
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                            <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent">
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                            <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red">
                            @error('image')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Source URL</label>
                        <input type="url" name="source_url" value="{{ old('source_url') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent" placeholder="https://...">
                        @error('source_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Meta --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center">⚙️ Options</h3>
                    <div class="flex items-center space-x-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="published" value="1" {{ old('published', true) ? 'checked' : '' }} class="rounded border-gray-300 text-senegal-red focus:ring-senegal-red mr-2">
                            <span class="text-sm font-medium text-gray-700">Publié immédiatement</span>
                        </label>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date de publication</label>
                            <input type="datetime-local" name="published_at" value="{{ old('published_at') }}" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red">
                        </div>
                    </div>
                </div>

                {{-- Tags --}}
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center">🏷️ Tags</h3>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tags (séparés par des virgules)</label>
                        <input type="text" name="tags" value="{{ old('tags') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red focus:border-transparent" placeholder="politique, sénégal, actualité">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex items-center space-x-4">
                    <button type="submit" class="bg-senegal-red text-white px-8 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors">
                        📤 Publier l'article
                    </button>
                    <button type="submit" name="save_draft" value="1" class="bg-gray-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-gray-700 transition-colors">
                        💾 Sauvegarder le brouillon
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="text-gray-600 hover:text-senegal-red transition-colors font-medium">Annuler</a>
                </div>
            </form>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="font-bold text-lg mb-4 flex items-center text-senegel-yellow">📊 Aperçu</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Statut:</span> <span id="statusPreview" class="font-bold text-green-600">Publié</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Créé le:</span> <span>{{ now()->format('d/m/Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Lu:</span> <span>0</span></div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="font-bold text-lg mb-4 flex items-center text-senegal-green">📋 Catégories</h3>
                <ul class="space-y-2 text-sm">
                    @foreach($categories as $category)
                        <li class="flex items-center space-x-2">
                            <span class="w-2 h-2 bg-senegal-red rounded-full"></span>
                            <span>{{ $category->name }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection
