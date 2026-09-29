@extends('layouts.app')

@section('title', $article->getTitle($locale) . ' | Xibar360')

@section('header_ad')
    @foreach($headerAds as $ad)
        <div class="bg-gray-200 py-2 text-center">
            <a href="{{ $ad->target_url ?? '#' }}" class="text-senegal-red font-bold text-sm">{{ $ad->title }}</a>
        </div>
    @endforeach
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    {{-- Breadcrumb --}}
    <nav class="flex items-center space-x-2 text-sm mb-6 text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-senegal-red transition-colors">Accueil</a>
        <span>→</span>
        <a href="{{ route('category.show', $article->category) }}" class="hover:text-senegal-red transition-colors">{{ $article->category?->name_fr ?? 'Actualité' }}</a>
        <span>→</span>
        <span class="text-senegal-red font-medium">{{ $article->getTitle($locale) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Left Column: Article --}}
        <div class="lg:col-span-2">
            {{-- Language Indicator --}}
            <div class="flex items-center space-x-2 mb-4">
                <a href="{{ route('lang.switch', ['locale' => 'fr']) }}" class="px-3 py-1 text-xs font-bold {{ app()->getLocale() === 'fr' ? 'bg-senegal-red text-white' : 'bg-white text-gray-700' }} rounded">FR</a>
                <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="px-3 py-1 text-xs font-bold {{ app()->getLocale() === 'en' ? 'bg-senegal-red text-white' : 'bg-white text-gray-700' }} rounded">EN</a>
                <span class="px-3 py-1 text-xs font-bold bg-gray-200 text-gray-600 rounded">{{ $article->language === 'fr' ? 'Français' : 'English' }}</span>
            </div>

            {{-- Category Badge --}}
            <div class="mb-4">
                <span class="inline-block bg-senegal-red text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                    {{ $article->category?->name_fr ?? 'Actualité' }}
                </span>
                @if($article->is_featured)
                    <span class="inline-block bg-senegal-yellow text-gray-900 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide ml-2">✨ Mis en avant</span>
                @endif
            </div>

            {{-- Article Title --}}
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                {{ $article->getTitle($locale) }}
            </h1>

            {{-- Meta Info --}}
            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-6 pb-6 border-b">
                <div class="flex items-center space-x-2">
                    @if($article->author)
                        <span class="font-medium">{{ $article->author->name }}</span>
                    @endif
                </div>
                <span>•</span>
                <time>{{ $article->publication_date?->format('d/m/Y') }}</time>
                <span>•</span>
                <span>{{ $article->source_type === 'imported' ? '🕐 Importé' : '📝 Rédaction' }}</span>
            </div>

            {{-- Thumbnail --}}
            @if($article->thumbnail)
                <div class="mb-6 rounded-xl overflow-hidden shadow-lg">
                    <img src="{{ $article->thumbnail }}" alt="{{ $article->getTitle($locale) }}" class="w-full h-64 md:h-96 object-cover">
                </div>
            @endif

            {{-- Summary --}}
            @if($article->getSummary($locale))
                <div class="bg-gray-50 border-l-4 border-senegal-yellow p-4 rounded-r-lg mb-6">
                    <p class="text-lg text-gray-700 italic">{{ $article->getSummary($locale) }}</p>
                </div>
            @endif

            {{-- Article Body --}}
            <div class="prose prose-lg max-w-none mb-8">
                {!! $article->getContent($locale) !!}
            </div>

            {{-- Source Link --}}
            @if($article->source_url)
                <div class="mb-8">
                    <a href="{{ $article->source_url }}" target="_blank" rel="noopener" class="inline-flex items-center text-senegal-red font-bold hover:underline">
                        🔗 Lire la source originale
                    </a>
                </div>
            @endif

            {{-- Social Sharing --}}
            <div class="mb-8 p-4 bg-gray-50 rounded-xl">
                <h3 class="font-bold mb-3">Partager cet article :</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($article->getTitle($locale)) }}" target="_blank" class="bg-blue-400 text-white px-4 py-2 rounded-lg hover:bg-blue-500 transition-colors text-sm font-medium">🐦 Twitter</a>
                    <a href="https://facebook.com/sharer/sharer.php?u={{ url()->current() }}" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">📘 Facebook</a>
                    <a href="https://wa.me/?text={{ urlencode($article->getTitle($locale) . ' - ' . url()->current()) }}" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">💬 WhatsApp</a>
                </div>
            </div>
        </div>

        {{-- Right Column: Related/Latest Articles --}}
        <div class="lg:col-span-1">
            {{-- Back Links --}}
            <div class="mb-6 flex flex-col gap-2">
                <a href="{{ route('category.show', $article->category) }}" class="inline-flex items-center text-senegal-red font-bold text-sm hover:underline">
                    ← Retour à {{ $article->category?->name_fr ?? 'la catégorie' }}
                </a>
                <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 font-bold text-sm hover:text-senegal-red transition-colors">
                    ← Accueil
                </a>
            </div>

            {{-- Related Articles --}}
            @if($related->isNotEmpty())
                <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                    <h3 class="font-bold text-lg mb-4 flex items-center text-senegal-red">
                        📰 Articles liés
                    </h3>
                    <div class="space-y-4">
                        @foreach($related as $relatedArticle)
                            <div class="pb-4 border-b border-gray-100 last:border-0 last:pb-0 hover:bg-gray-50 -mx-2 px-2 py-2 rounded-lg transition-colors cursor-pointer">
                                <a href="{{ route('articles.show', $relatedArticle) }}" class="block">
                                    <h4 class="font-bold text-sm leading-snug mb-1 hover:text-senegal-red transition-colors line-clamp-2">{{ $relatedArticle->getTitle($locale) }}</h4>
                                    <p class="text-xs text-gray-500">{{ $relatedArticle->publication_date?->format('d/m/Y') }}</p>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Latest Articles --}}
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="font-bold text-lg mb-4 flex items-center text-senegal-green">
                    🔥 Derniers articles
                </h3>
                <div class="space-y-4">
                    @foreach($latestArticles as $latest)
                        <div class="pb-4 border-b border-gray-100 last:border-0 last:pb-0 hover:bg-gray-50 -mx-2 px-2 py-2 rounded-lg transition-colors cursor-pointer">
                            <a href="{{ route('articles.show', $latest) }}" class="block">
                                <h4 class="font-bold text-sm leading-snug mb-1 hover:text-senegal-red transition-colors line-clamp-2">{{ $latest->getTitle($locale) }}</h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span>{{ $latest->category?->name_fr ?? '' }}</span>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Sidebar Ad --}}
            @if($sidebarAds->isNotEmpty())
                @foreach($sidebarAds as $ad)
                    <div class="bg-white rounded-xl p-6 shadow-md mb-4">
                        <a href="{{ $ad->target_url ?? '#' }}" class="text-senegal-red font-bold">{{ $ad->title }}</a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
