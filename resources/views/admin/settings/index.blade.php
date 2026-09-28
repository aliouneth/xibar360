@extends('layouts.admin')

@section('title', __('Settings'))

@section('content')
<h1>{{ __('Site Settings') }}</h1>
<div class="card">
    <div class="card-body">
        <form>
            <div class="mb-3">
                <label class="form-label">{{ __('Site Name') }}</label>
                <input type="text" class="form-control" value="{{ \App\Models\Setting::getByKey('site_name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('Default Language') }}</label>
                <select class="form-select">
                    <option value="fr" {{ \App\Models\Setting::getByKey('default_language') === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="en" {{ \App\Models\Setting::getByKey('default_language') === 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">{{ __('Save Settings') }}</button>
        </form>
    </div>
</div>
@endsection
