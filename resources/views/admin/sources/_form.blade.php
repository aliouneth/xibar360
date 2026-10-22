@php($source = $source ?? null)

<div class="mb-3">
    <label class="form-label">{{ __('Name') }}</label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $source->name ?? '') }}" required>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Feed URL') }}</label>
    <input type="url" name="url" class="form-control @error('url') is-invalid @enderror" value="{{ old('url', $source->url ?? '') }}" placeholder="https://example.com/feed" required>
    @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">{{ __('RSS or Atom feed URL.') }}</div>
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Language') }}</label>
    <select name="language" class="form-select @error('language') is-invalid @enderror">
        <option value="fr" {{ old('language', $source->language ?? 'fr') === 'fr' ? 'selected' : '' }}>{{ __('French') }}</option>
        <option value="en" {{ old('language', $source->language ?? 'fr') === 'en' ? 'selected' : '' }}>{{ __('English') }}</option>
    </select>
    @error('language')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Custom User Agent') }}</label>
    <input type="text" name="user_agent" class="form-control @error('user_agent') is-invalid @enderror" value="{{ old('user_agent', $source->user_agent ?? '') }}">
    @error('user_agent')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <div class="form-text">{{ __('Optional. Some publishers reject the default bot user agent.') }}</div>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="enabled" value="1" class="form-check-input" id="enabled" {{ old('enabled', $source->enabled ?? true) ? 'checked' : '' }}>
    <label class="form-check-label" for="enabled">{{ __('Enabled') }}</label>
</div>
