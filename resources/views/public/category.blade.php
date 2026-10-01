@extends('layouts.app')

@section('title', $category->name_fr . ' | Xibar360')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ $category->name_fr }}</h1>
        <p class="text-gray-500 mt-2">{{ $articles->total() }} articles</p>
    </div>

    @if($articles->isEmpty())
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">{{ __('Aucun article dans cette catégorie pour le moment.') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $article)
                <a href="{{ route('articles.show', $article) }}"
                   class="card-hover bg-white rounded-xl shadow-md overflow-hidden flex flex-col h-full">
                    @if($article->thumbnail)
                        <img src="{{ $article->thumbnail }}" alt=""
                             class="h-48 w-full object-cover" loading="lazy"
                             onerror="this.style.display='none'">
                    @endif
                    <div class="p-4 flex-1 flex flex-col">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="category-badge bg-senegal-red text-white">{{ $article->category?->name_fr ?? 'Actualité' }}</span>
                            @if($article->source_name)
                                <span class="text-xs text-gray-500">{{ $article->source_name }}</span>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-3">
                            {{ $article->title_fr ?? $article->title_en }}
                        </h3>
                        <p class="text-gray-600 text-sm line-clamp-2 flex-1">
                            {{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 140) }}
                        </p>
                        <div class="flex items-center text-xs text-gray-500 mt-3">
                            <span>{{ $article->publication_date?->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
