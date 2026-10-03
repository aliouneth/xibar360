@extends('layouts.admin')

@section('title', __('Create Article'))

@section('content')
<h1>{{ __('Create Article') }}</h1>

<a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-outline-secondary mb-3">{{ __('Back to Articles') }}</a>

<form method="POST" action="{{ route('admin.articles.store') }}" class="mb-4" enctype="multipart/form-data">
    @csrf

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Title (French)') }} <span class="text-danger">*</span></label>
            <input type="text" name="title_fr" class="form-control @error('title_fr') is-invalid @enderror" value="{{ old('title_fr') }}" required>
            @error('title_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Title (English)') }} <span class="text-danger">*</span></label>
            <input type="text" name="title_en" class="form-control @error('title_en') is-invalid @enderror" value="{{ old('title_en') }}" required>
            @error('title_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Summary (French)') }}</label>
            <textarea name="summary_fr" rows="3" class="form-control @error('summary_fr') is-invalid @enderror">{{ old('summary_fr') }}</textarea>
            @error('summary_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Summary (English)') }}</label>
            <textarea name="summary_en" rows="3" class="form-control @error('summary_en') is-invalid @enderror">{{ old('summary_en') }}</textarea>
            @error('summary_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Content (French)') }} <span class="text-danger">*</span></label>
            <textarea name="content_fr" rows="8" class="form-control @error('content_fr') is-invalid @enderror" required>{{ old('content_fr') }}</textarea>
            @error('content_fr')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Content (English)') }} <span class="text-danger">*</span></label>
            <textarea name="content_en" rows="8" class="form-control @error('content_en') is-invalid @enderror" required>{{ old('content_en') }}</textarea>
            @error('content_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">{{ __('Category') }} <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                <option value="">{{ __('Select category') }}</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name_fr }}</option>
                @endforeach
            </select>
            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">{{ __('Language') }} <span class="text-danger">*</span></label>
            <select name="language" class="form-select @error('language') is-invalid @enderror" required>
                <option value="">{{ __('Select language') }}</option>
                <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>Français</option>
                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
            </select>
            @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-4 mb-3">
            <label class="form-label">{{ __('Source Type') }} <span class="text-danger">*</span></label>
            <select name="source_type" class="form-select @error('source_type') is-invalid @enderror" required>
                <option value="">{{ __('Select source type') }}</option>
                <option value="local" {{ old('source_type') == 'local' ? 'selected' : '' }}>{{ __('Local') }}</option>
                <option value="imported" {{ old('source_type') == 'imported' ? 'selected' : '' }}>{{ __('Imported') }}</option>
            </select>
            @error('source_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Publication Date') }} <span class="text-danger">*</span></label>
            <input type="datetime-local" name="publication_date" class="form-control @error('publication_date') is-invalid @enderror" value="{{ old('publication_date', now()->format('Y-m-d\TH:i')) }}" required>
            @error('publication_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Source URL') }}</label>
            <input type="url" name="source_url" class="form-control @error('source_url') is-invalid @enderror" value="{{ old('source_url') }}" placeholder="https://example.com/article">
            @error('source_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Thumbnail') }}</label>
            <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror" accept="image/*">
            @error('thumbnail')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text">{{ __('Max 2MB. Will be stored in storage/app/public/articles/') }}</div>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">{{ __('Source Name') }}</label>
            <input type="text" name="source_name" class="form-control" value="{{ old('source_name') }}" placeholder="{{ __('e.g. Le Monde, Reuters') }}">
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3 form-check mt-4">
            <input type="checkbox" name="is_published" class="form-check-input" id="is_published" {{ old('is_published', false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_published">{{ __('Published') }}</label>
        </div>

        <div class="col-md-6 mb-3 form-check mt-4">
            <input type="checkbox" name="is_featured" class="form-check-input" id="is_featured" {{ old('is_featured', false) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_featured">{{ __('Featured') }}</label>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">{{ __('Create Article') }}</button>
</form>
@endsection