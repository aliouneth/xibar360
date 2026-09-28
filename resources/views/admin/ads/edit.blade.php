@extends('layouts.admin')

@section('title', __('Edit Ad'))

@section('content')
<h1>{{ __('Edit Ad') }}</h1>

<a href="{{ route('admin.ads.index') }}" class="btn btn-sm btn-outline-secondary mb-3">{{ __('Back to Ads') }}</a>

<form method="POST" action="{{ route('admin.ads.update', $ad) }}" class="mb-4" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">{{ __('Title') }}</label>
        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $ad->title) }}">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $ad->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Zone') }}</label>
            <select name="zone" class="form-select @error('zone') is-invalid @enderror">
                @foreach(['header', 'sidebar', 'inline', 'footer'] as $z)
                    <option value="{{ $z }}" {{ old('zone', $ad->zone) == $z ? 'selected' : '' }}>{{ ucfirst($z) }}</option>
                @endforeach
            </select>
            @error('zone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Order') }}</label>
            <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $ad->order) }}" min="0">
            @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Target URL') }}</label>
        <input type="url" name="target_url" class="form-control @error('target_url') is-invalid @enderror" value="{{ old('target_url', $ad->target_url) }}">
        @error('target_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Image') }}</label>
        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('HTML Snippet') }}</label>
        <textarea name="html_snippet" rows="4" class="form-control @error('html_snippet') is-invalid @enderror">{{ old('html_snippet', $ad->html_snippet) }}</textarea>
        @error('html_snippet')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $ad->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('Update Ad') }}</button>
</form>
@endsection
