@extends('layouts.app')

@section('title', __('Home') | __('SunuNews'))

@section('content')
<div class="row">
    @if(!empty($ads))
    <div class="col-12 mb-4">
        @foreach($ads->take(3) as $ad)
        <div class="alert alert-info">{{ $ad->title }}</div>
        @endforeach
    </div>
    @endif

    @if(!empty($featuredArticles))
    <div class="col-12 mb-4">
        <h2>{{ __('Featured Articles') }}</h2>
        <div class="row">
            @foreach($featuredArticles as $article)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $article->getTitle($locale) }}</h5>
                        <p class="card-text">{{ Str::limit($article->getSummary($locale), 150) }}</p>
                        <a href="{{ route('articles.show', $article) }}" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @foreach($columns as $column)
    <div class="col-md-4 mb-4">
        <h3>{{ $column['category']->name_fr ?? $column['category']->name_en }}</h3>
        @foreach($column['articles'] as $article)
        <div class="card mb-2">
            <div class="card-body">
                <h6 class="card-title">
                    <a href="{{ route('articles.show', $article) }}">{{ $article->getTitle($locale) }}</a>
                </h6>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>
@endsection
