@extends('layouts.app')

@section('title', __('Search Results') . ' | SunuNews')

@section('content')
<h1>{{ __('Search Results') }}</h1>
@if($query)
<p>{{ __('Results for') }}: "{{ $query }}"</p>
<form action="{{ route('search.results') }}" method="GET" class="mb-3">
    <div class="input-group">
        <input type="text" name="q" class="form-control" value="{{ $query }}">
        <select name="language" class="form-select">
            <option value="">{{ __('All Languages') }}</option>
            <option value="fr" {{ $language === 'fr' ? 'selected' : '' }}>Français</option>
            <option value="en" {{ $language === 'en' ? 'selected' : '' }}>English</option>
        </select>
        <select name="category" class="form-select">
            <option value="">{{ __('All Categories') }}</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $category == $cat->id ? 'selected' : '' }}>{{ $cat->name_fr }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
    </div>
</form>
@endif
<div class="row">
    @foreach($articles as $article)
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="{{ route('articles.show', $article) }}">{{ $article->getTitle($locale) }}</a>
                </h5>
                <p class="card-text">{{ Str::limit($article->getSummary($locale), 150) }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
{{ $articles->links() }}
@endsection
