@extends('layouts.admin')

@section('title', __('Edit Source'))

@section('content')
<h1>{{ __('Edit Source') }}</h1>

<a href="{{ route('admin.sources.index') }}" class="btn btn-sm btn-outline-secondary mb-3">{{ __('Back to Sources') }}</a>

<form method="POST" action="{{ route('admin.sources.update', $source) }}" class="mb-4">
    @csrf
    @method('PUT')
    @include('admin.sources._form', ['source' => $source])

    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
</form>
@endsection
