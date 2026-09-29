@extends('layouts.admin')

@section('title', __('Settings'))

@section('content')
<h1>{{ __('Site Settings') }}</h1>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.settings.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">{{ __('Site Name') }}</label>
                <input type="text" name="site_name" class="form-control" value="{{ old('site_name', \App\Models\Setting::getByKey('site_name')) }}">
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Default Language') }}</label>
                <select name="default_language" class="form-select">
                    <option value="fr" {{ old('default_language', \App\Models\Setting::getByKey('default_language')) === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="en" {{ old('default_language', \App\Models\Setting::getByKey('default_language')) === 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
        </form>
    </div>
</div>
@endsection
