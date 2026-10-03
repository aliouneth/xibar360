@extends('layouts.admin')

@section('title', __('Articles'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>{{ __('Articles') }}</h1>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">{{ __('Create Article') }}</a>
</div>

<form method="GET" class="row g-3 mb-4" id="filter-form">
    <div class="col-md-3">
        <label class="form-label visually-hidden">{{ __('Search') }}</label>
        <input type="text" name="search" class="form-control" placeholder="{{ __('Search title...') }}" value="{{ request('search') }}">
    </div>
    <div class="col-md-2">
        <label class="form-label visually-hidden">{{ __('Category') }}</label>
        <select name="category" class="form-select">
            <option value="">{{ __('All Categories') }}</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name_fr }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label visually-hidden">{{ __('Language') }}</label>
        <select name="language" class="form-select">
            <option value="">{{ __('All Languages') }}</option>
            <option value="fr" {{ request('language') == 'fr' ? 'selected' : '' }}>Français</option>
            <option value="en" {{ request('language') == 'en' ? 'selected' : '' }}>English</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label visually-hidden">{{ __('Source Type') }}</label>
        <select name="source_type" class="form-select">
            <option value="">{{ __('All Sources') }}</option>
            <option value="imported" {{ request('source_type') == 'imported' ? 'selected' : '' }}>{{ __('Imported') }}</option>
            <option value="local" {{ request('source_type') == 'local' ? 'selected' : '' }}>{{ __('Local') }}</option>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label visually-hidden">{{ __('Status') }}</label>
        <select name="status" class="form-select">
            <option value="">{{ __('All Statuses') }}</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('Published') }}</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('Draft') }}</option>
        </select>
    </div>
    <div class="col-md-1">
        <button type="submit" class="btn btn-outline-secondary w-100">{{ __('Filter') }}</button>
    </div>
</form>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Thumbnail') }}</th>
                <th>{{ __('Title') }}</th>
                <th class="d-none d-md-table-cell">{{ __('Category') }}</th>
                <th class="d-none d-lg-table-cell">{{ __('Language') }}</th>
                <th class="d-none d-lg-table-cell">{{ __('Source') }}</th>
                <th class="d-none d-lg-table-cell">{{ __('Status') }}</th>
                <th class="d-none d-xl-table-cell">{{ __('Date') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($articles as $article)
            <tr>
                <td>{{ $article->id }}</td>
                <td>
                    @if($article->thumbnail)
                        <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="" class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <strong>{{ $article->title_fr }}</strong>
                    @if($article->title_en)
                        <br><small class="text-muted">{{ $article->title_en }}</small>
                    @endif
                </td>
                <td class="d-none d-md-table-cell">{{ $article->category?->name_fr ?? '-' }}</td>
                <td class="d-none d-lg-table-cell">
                    <span class="badge bg-{{ $article->language === 'fr' ? 'primary' : 'secondary' }}">{{ strtoupper($article->language) }}</span>
                </td>
                <td class="d-none d-lg-table-cell">
                    <span class="badge bg-{{ $article->source_type === 'imported' ? 'info' : 'success' }}">{{ ucfirst($article->source_type) }}</span>
                    @if($article->source_name)
                        <br><small class="text-muted">{{ $article->source_name }}</small>
                    @endif
                </td>
                <td class="d-none d-lg-table-cell">
                    @if($article->is_published)
                        <span class="badge bg-success">{{ __('Published') }}</span>
                    @else
                        <span class="badge bg-warning">{{ __('Draft') }}</span>
                    @endif
                </td>
                <td class="d-none d-xl-table-cell">{{ $article->publication_date?->format('Y-m-d H:i') ?? '-' }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-outline-primary" title="{{ __('View') }}"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-outline-secondary" title="{{ __('Edit') }}"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.articles.toggle-publish', $article) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-{{ $article->is_published ? 'warning' : 'success' }}" title="{{ $article->is_published ? __('Unpublish') : __('Publish') }}">
                                <i class="fas fa-{{ $article->is_published ? 'times' : 'check' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" title="{{ __('Delete') }}"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center">{{ __('No articles found') }}</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $articles->appends(request()->query())->links() }}
</div>
@endsection