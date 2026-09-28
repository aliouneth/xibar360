@extends('layouts.admin')

@section('title', __('Articles'))

@section('content')
<h1>{{ __('Articles') }}</h1>
<a href="{{ route('admin.articles.create') }}" class="btn btn-primary mb-3">{{ __('Create Article') }}</a>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Language') }}</th>
                <th>{{ __('Source') }}</th>
                <th>{{ __('Published') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($articles as $article)
            <tr>
                <td>{{ $article->id }}</td>
                <td>{{ $article->getTitle(app()->getLocale()) }}</td>
                <td>{{ $article->category->name_fr ?? '' }}</td>
                <td>{{ $article->language }}</td>
                <td>{{ $article->source_type }}</td>
                <td>
                    <form action="{{ route('admin.articles.toggle', $article) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $article->is_published ? 'btn-success' : 'btn-secondary' }}">
                            {{ $article->is_published ? 'Yes' : 'No' }}
                        </button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $articles->links() }}
</div>
@endsection
