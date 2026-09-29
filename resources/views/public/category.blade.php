@extends('layouts.app')

@section('title', __('Category') . ' | Xibar360')

@section('content')
<h1>{{ $category->name_fr ?? $category->name_en }}</h1>
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
