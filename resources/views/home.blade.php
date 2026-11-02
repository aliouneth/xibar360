@extends('layouts.app')

@section('title', 'Accueil')

@section('header_ad')
    @include('ads.header')
@endsection

@section('content')
    @php
        $articlePlaceholder = asset('images/article-placeholder.svg');
    @endphp

    {{-- Hero Section --}}
    @if($featured->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 pt-6 pb-0">
        {{-- Featured Slider --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8 mb-4">
            @foreach($featured->take(2) as $article)
                <a href="{{ route('articles.show', $article) }}" class="card-hover bg-white rounded-xl shadow-md overflow-hidden block">
                    <img src="{{ $article->thumbnailSrc() ?: $articlePlaceholder }}" alt=""
                         class="h-64 w-full object-cover"
                         onerror="this.onerror=null;this.src='{{ $articlePlaceholder }}';">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3 flex-wrap">
                            <span class="category-badge bg-senegal-red text-white inline-block">{{ $article->category?->name_fr ?? 'Actualité' }}</span>
                            @if($article->source_name)
                                <span class="text-xs text-gray-500">{{ $article->source_name }}</span>
                            @endif
                        </div>
                        <h2 class="text-xl font-bold mb-2 text-gray-900">{{ $article->title_fr ?? $article->title_en }}</h2>
                        <p class="text-gray-600 text-sm line-clamp-3">{{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 150) }}</p>
                        <div class="flex items-center mt-4 text-sm text-gray-500">
                            <span>{{ $article->publication_date?->format('d/m/Y H:i') }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ $article->language === 'fr' ? 'Français' : 'English' }}</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Inline Ads --}}
    @if(($inlineAds ?? collect())->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 pb-8">
        @include('ads.zone', ['zone' => 'inline', 'ads' => $inlineAds])
    </div>
    @endif

    {{-- Latest feed: everything imported, newest publication date first --}}
    <div class="max-w-7xl mx-auto px-4 pb-12">
        @if($latest->isEmpty())
            <p class="text-gray-500">{{ __('Aucun article pour le moment.') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latest as $article)
                    <a href="{{ route('articles.show', $article) }}"
                       class="card-hover bg-white rounded-xl shadow-md overflow-hidden flex flex-col">
                        @if($article->thumbnailSrc())
                            <img src="{{ $article->thumbnailSrc() }}" alt=""
                                 class="h-40 w-full object-cover" loading="lazy"
                                 onerror="this.style.display='none'">
                        @endif
                        <div class="p-4 flex-1 flex flex-col">
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="category-badge bg-senegal-red text-white">{{ $article->category?->name_fr ?? __('Actualité') }}</span>
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
                {{ $latest->links() }}
            </div>
        @endif
    </div>

    {{-- Category sections: every active category gets its own cell in one
         grid, so a category created in the admin panel shows up automatically
         and no column is left half empty on narrower screens. --}}
    @php
        $categoryIcons = [
            'POLITIQUE' => '🏛️',
            'ÉCONOMIE' => '💰',
            'SOCIÉTÉ' => '👥',
            'SPORT' => '⚽',
            'AFRIQUE' => '🌍',
            'MONDE' => '🌐',
            'PEOPLE' => '🌟',
        ];
    @endphp

    <div class="max-w-7xl mx-auto px-4 pb-12">
        @if(($sidebarAds ?? collect())->isNotEmpty())
            <div class="mb-8">
                @include('ads.zone', ['zone' => 'sidebar', 'ads' => $sidebarAds])
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 items-start">
            @foreach($categories as $cat)
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-senegal-red flex items-center">
                            <span class="mr-2" aria-hidden="true">{{ $categoryIcons[$cat->name_fr] ?? '📰' }}</span>
                            {{ $cat->name_fr }}
                        </h2>
                        <a href="{{ route('category.show', $cat) }}" class="text-sm text-senegal-red hover:underline">Voir plus →</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($sections[$cat->name_fr] ?? collect() as $article)
                            <a href="{{ route('articles.show', $article) }}" class="card-hover block bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <h3 class="font-bold text-gray-900 mb-2">{{ $article->title_fr ?? $article->title_en }}</h3>
                                <p class="text-gray-600 text-sm line-clamp-2">{{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 100) }}</p>
                            </a>
                        @empty
                            <p class="text-sm text-gray-500">{{ __('Aucun article dans cette rubrique pour le moment.') }}</p>
                        @endforelse
                    </div>
                </div>
            @endforeach

            @if($localPicks->isNotEmpty())
                {{-- Local Editor Picks: only rendered when such articles exist --}}
                <div>
                    <h2 class="text-xl font-bold text-senegal-green mb-4 flex items-center">📝 Rédaction</h2>
                    <div class="space-y-4">
                        @foreach($localPicks as $article)
                            <a href="{{ route('articles.show', $article) }}" class="card-hover block bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                <h3 class="font-bold text-gray-900 mb-2">{{ $article->title_fr ?? $article->title_en }}</h3>
                                <p class="text-gray-600 text-sm line-clamp-2">{{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 100) }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection