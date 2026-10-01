@extends('layouts.admin')

@section('title', __('Edit Category'))

@section('content')
<h1>{{ __('Edit Category') }}</h1>

<a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary mb-3">{{ __('Back to Categories') }}</a>

<form method="POST" action="{{ route('admin.categories.update', $category) }}" class="mb-4">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">{{ __('Name (French)') }}</label>
        <input type="text" name="name_fr" class="form-control @error('name_fr') is-invalid @enderror" value="{{ old('name_fr', $category->name_fr) }}" required>
        @error('name_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Name (English)') }}</label>
        <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en', $category->name_en) }}" required>
        @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Slug') }}</label>
        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $category->slug) }}" required>
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Description') }}</label>
        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('Icon') }}</label>
        <input type="text" name="icon" class="form-control @error('icon') is-invalid @enderror" value="{{ old('icon', $category->icon) }}" placeholder="e.g. fas fa-newspaper">
        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('Update Category') }}</button>
</form>
@endsection