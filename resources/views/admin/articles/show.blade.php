@extends('layouts.admin')

@section('title', __('Article Details'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Article Details') }}</h1>
    <div>
        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-outline-secondary">{{ __('Edit') }}</a>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary ms-2">{{ __('Back') }}</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header">{{ __('Title (French)') }}</div>
            <div class="card-body">
                <h3>{{ $article->title_fr }}</h3>
            </div>
        </div>

        @if($article->title_en)
        <div class="card mb-3">
            <div class="card-header">{{ __('Title (English)') }}</div>
            <div class="card-body">
                <h3>{{ $article->title_en }}</h3>
            </div>
        </div>
        @endif

        @if($article->summary_fr)
        <div class="card mb-3">
            <div class="card-header">{{ __('Summary (French)') }}</div>
            <div class="card-body">
                <p>{{ $article->summary_fr }}</p>
            </div>
        </div>
        @endif

        @if($article->summary_en)
        <div class="card mb-3">
            <div class="card-header">{{ __('Summary (English)') }}</div>
            <div class="card-body">
                <p>{{ $article->summary_en }}</p>
            </div>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-header">{{ __('Content (French)') }}</div>
            <div class="card-body">
                <div class="prose">{{ $article->content_fr }}</div>
            </div>
        </div>

        @if($article->content_en)
        <div class="card mb-3">
            <div class="card-header">{{ __('Content (English)') }}</div>
            <div class="card-body">
                <div class="prose">{{ $article->content_en }}</div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">{{ __('Information') }}</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('ID') }}</dt>
                    <dd class="col-sm-8">{{ $article->id }}</dd>

                    <dt class="col-sm-4">{{ __('Category') }}</dt>
                    <dd class="col-sm-8">{{ $article->category?->name_fr ?? '-' }}</dd>

                    <dt class="col-sm-4">{{ __('Language') }}</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $article->language === 'fr' ? 'primary' : 'secondary' }}">{{ strtoupper($article->language) }}</span>
                    </dd>

                    <dt class="col-sm-4">{{ __('Source Type') }}</dt>
                    <dd class="col-sm-8">
                        <span class="badge bg-{{ $article->source_type === 'imported' ? 'info' : 'success' }}">{{ ucfirst($article->source_type) }}</span>
                    </dd>

                    <dt class="col-sm-4">{{ __('Source Name') }}</dt>
                    <dd class="col-sm-8">{{ $article->source_name ?? '-' }}</dd>

                    <dt class="col-sm-4">{{ __('Source URL') }}</dt>
                    <dd class="col-sm-8">
                        @if($article->source_url)
                            <a href="{{ $article->source_url }}" target="_blank">{{ $article->source_url }}</a>
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('External ID') }}</dt>
                    <dd class="col-sm-8">{{ $article->external_id ?? '-' }}</dd>

                    <dt class="col-sm-4">{{ __('Publication Date') }}</dt>
                    <dd class="col-sm-8">{{ $article->publication_date?->format('Y-m-d H:i') ?? '-' }}</dd>

                    <dt class="col-sm-4">{{ __('Author') }}</dt>
                    <dd class="col-sm-8">{{ $article->author?->name ?? '-' }}</dd>

                    <dt class="col-sm-4">{{ __('Status') }}</dt>
                    <dd class="col-sm-8">
                        @if($article->is_published)
                            <span class="badge bg-success">{{ __('Published') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('Draft') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('Featured') }}</dt>
                    <dd class="col-sm-8">
                        @if($article->is_featured)
                            <span class="badge bg-warning text-dark">{{ __('Yes') }}</span>
                        @else
                            <span class="badge bg-secondary">{{ __('No') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('Created') }}</dt>
                    <dd class="col-sm-8">{{ $article->created_at?->format('Y-m-d H:i') }}</dd>

                    <dt class="col-sm-4">{{ __('Updated') }}</dt>
                    <dd class="col-sm-8">{{ $article->updated_at?->format('Y-m-d H:i') }}</dd>
                </dl>
            </div>
        </div>

        @if($article->thumbnail)
        <div class="card mb-3">
            <div class="card-header">{{ __('Thumbnail') }}</div>
            <div class="card-body text-center">
                <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="" class="img-fluid" style="max-height: 300px;">
            </div>
        </div>
        @endif

        <div class="card mb-3">
            <div class="card-header">{{ __('Actions') }}</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form action="{{ route('admin.articles.toggle-publish', $article) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-{{ $article->is_published ? 'warning' : 'success' }} w-100">
                            <i class="fas fa-{{ $article->is_published ? 'times' : 'check' }} me-1"></i>
                            {{ $article->is_published ? __('Unpublish') : __('Publish') }}
                        </button>
                    </form>
                    <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this article?') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-1"></i>{{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection