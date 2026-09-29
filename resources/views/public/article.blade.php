@extends('layouts.app')

@section('title', $article->getTitle($locale) . ' | Xibar360')

@section('content')
<article>
    <h1>{{ $article->getTitle($locale) }}</h1>
    <p class="text-muted">
        {{ $article->category->name_fr ?? '' }} | {{ $article->publication_date }}
    </p>
    <div class="mb-4">
        {!! $article->getContent($locale) !!}
    </div>
    @if($article->source_url)
    <a href="{{ $article->source_url }}" class="btn btn-outline-primary">{{ __('Read Original') }}</a>
    @endif
</article>
<hr>
<div class="mt-4">
    <h3>{{ __('Related Articles') }}</h3>
    @foreach($article->category->articles()->published()->where('id', '!=', $article->id)->take(3)->get() as $related)
    <div class="card mb-2">
        <div class="card-body">
            <a href="{{ route('articles.show', $related) }}">{{ $related->getTitle($locale) }}</a>
        </div>
    </div>
    @endforeach
</div>
@endsection
