@extends('layouts.admin')

@section('title', __('Add Source'))

@section('content')
<h1>{{ __('Add Source') }}</h1>

<a href="{{ route('admin.sources.index') }}" class="btn btn-sm btn-outline-secondary mb-3">{{ __('Back to Sources') }}</a>

<form method="POST" action="{{ route('admin.sources.store') }}" class="mb-4">
    @csrf
    @include('admin.sources._form', ['source' => null])

    <button type="submit" class="btn btn-primary">{{ __('Add Source') }}</button>
</form>
@endsection
