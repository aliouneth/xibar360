@extends('layouts.app')

@section('title', 'Accueil - SunuNews')

@section('header_ad')
    @foreach($headerAds as $ad)
        <div class="bg-gray-200 py-2 text-center">
            <a href="{{ $ad->target_url ?? '#' }}" class="text-senegal-red font-bold text-sm">{{ $ad->title }}</a>
        </div>
    @endforeach
@endsection

@section('content')
    {{-- Hero Section --}}
    @if($featured->isNotEmpty())
    <div class="max-w-7xl mx-auto px-4 py-8">
        {{-- Featured Slider --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            @foreach($featured->take(2) as $article)
                <a href="{{ route('articles.show', $article) }}" class="card-hover bg-white rounded-xl shadow-md overflow-hidden block">
                    @if($article->thumbnail)
                        <img src="{{ $article->thumbnail }}" alt=""
                             class="h-64 w-full object-cover"
                             onerror="this.style.display='none'">
                    @else
                        <div class="h-64 bg-gray-200 flex items-center justify-center text-gray-400 text-6xl">
                            📰
                        </div>
                    @endif
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

    {{-- Latest feed: everything imported, newest publication date first --}}
    <div class="max-w-7xl mx-auto px-4 pb-12">
        @if($latest->isEmpty())
            <p class="text-gray-500">{{ __('Aucun article pour le moment.') }}</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($latest as $article)
                    <a href="{{ route('articles.show', $article) }}"
                       class="card-hover bg-white rounded-xl shadow-md overflow-hidden flex flex-col">
                        @if($article->thumbnail)
                            <img src="{{ $article->thumbnail }}" alt=""
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

    {{-- Three Column Layout --}}
    <div class="max-w-7xl mx-auto px-4 pb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Column 1: POLITIQUE, ÉCONOMIE --}}
            <div class="lg:col-span-1 space-y-8">
                @foreach(['POLITIQUE', 'ÉCONOMIE'] as $catName)
                    @php($cat = $categories->firstWhere('name_fr', $catName))
                    @if($cat)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-senegal-red flex items-center">
                                    @if($catName === 'POLITIQUE') 🏛️ @else 💰 @endif
                                    {{ $cat->name_fr }}
                                </h2>
                                <a href="{{ route('category.show', $cat) }}" class="text-sm text-senegal-red hover:underline">Voir plus →</a>
                            </div>
                            <div class="space-y-4">
                                @foreach($sections[$catName] ?? collect() as $article)
                                    <a href="{{ route('articles.show', $article) }}" class="card-hover block bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                        <h3 class="font-bold text-gray-900 mb-2">{{ $article->title_fr ?? $article->title_en }}</h3>
                                        <p class="text-gray-600 text-sm line-clamp-2">{{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 100) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Column 2: SOCIÉTÉ, SPORT, AFRIQUE --}}
            <div class="lg:col-span-1 space-y-8">
                @foreach(['SOCIÉTÉ', 'SPORT', 'AFRIQUE'] as $catName)
                    @php($cat = $categories->firstWhere('name_fr', $catName))
                    @if($cat)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                    @if($catName === 'SOCIÉTÉ') 👥 @elseif($catName === 'SPORT') ⚽ @else 🌍 @endif
                                    {{ $cat->name_fr }}
                                </h2>
                                <a href="{{ route('category.show', $cat) }}" class="text-sm text-senegal-red hover:underline">Voir plus →</a>
                            </div>
                            <div class="space-y-4">
                                @foreach($sections[$catName] ?? collect() as $article)
                                    <a href="{{ route('articles.show', $article) }}" class="card-hover block bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                        <h3 class="font-bold text-gray-900 mb-2">{{ $article->title_fr ?? $article->title_en }}</h3>
                                        <p class="text-gray-600 text-sm line-clamp-2">{{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 100) }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Column 3: MONDE, PEOPLE, Local --}}
            <div class="lg:col-span-1 space-y-8">
                <div class="lg:col-span-1 space-y-8">
                    @foreach(['MONDE', 'PEOPLE'] as $catName)
                        @php($cat = $categories->firstWhere('name_fr', $catName))
                        @if($cat)
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                                        @if($catName === 'MONDE') 🌐 @else 🌟 @endif
                                        {{ $cat->name_fr }}
                                    </h2>
                                    <a href="{{ route('category.show', $cat) }}" class="text-sm text-senegal-red hover:underline">Voir plus →</a>
                                </div>
                            <div class="space-y-4">
                                @foreach($sections[$catName] ?? collect() as $article)
                                        <a href="{{ route('articles.show', $article) }}" class="card-hover block bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                                            <h3 class="font-bold text-gray-900 mb-2">{{ $article->title_fr ?? $article->title_en }}</h3>
                                            <p class="text-gray-600 text-sm line-clamp-2">{{ $article->summary_fr ?? substr(strip_tags($article->content_fr), 100) }}</p>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                    
                    {{-- Local Editor Picks: only rendered when such articles exist --}}
                    @if($localPicks->isNotEmpty())
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
    </div>

    {{-- Sidebar Ads --}}
        @if($sidebarAds->isNotEmpty())
        <div class="mt-8 space-y-4">
            @foreach($sidebarAds as $ad)
                <div class="bg-white rounded-xl shadow-md p-4 text-center">
                    <a href="{{ $ad->target_url ?? '#' }}" class="text-senegal-red font-bold">{{ $ad->title }}</a>
                </div>
            @endforeach
        </div>
        @endif
    </div>
@endsection