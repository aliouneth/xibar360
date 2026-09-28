{{-- Shared by the user create and edit forms.
     $user is only present when editing; $roles is the full role list. --}}
@php($editing = isset($user))

<div class="card mb-4">
    <div class="card-header">{{ __('Account') }}</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">{{ __('Name') }} *</label>
                <input type="text" name="name" id="name" required
                       value="{{ old('name', $user->name ?? '') }}"
                       class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="email">{{ __('Email') }} *</label>
                <input type="email" name="email" id="email" required
                       value="{{ old('email', $user->email ?? '') }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">{{ __('Password') }}</div>
    <div class="card-body">
        @if($editing)
            <p class="text-muted small mb-3">
                {{ __('Leave both fields empty to keep the current password.') }}
            </p>
        @endif

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="password">
                    {{ __('New password') }} {{ $editing ? '' : '*' }}
                </label>
                <input type="password" name="password" id="password" autocomplete="new-password"
                       @unless($editing) required @endunless
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">
                    {{ __('Confirm password') }} {{ $editing ? '' : '*' }}
                </label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                       autocomplete="new-password"
                       @unless($editing) required @endunless
                       class="form-control">
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">{{ __('Roles') }}</div>
    <div class="card-body">
        @php($selected = old('roles', $editing ? $user->roles->pluck('id')->all() : []))

        @forelse($roles as $role)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roles[]"
                       value="{{ $role->id }}" id="role_{{ $role->id }}"
                       @checked(in_array($role->id, (array) $selected))>
                <label class="form-check-label" for="role_{{ $role->id }}">
                    {{ $role->name }}
                    @if($role->description)
                        <span class="text-muted small">— {{ $role->description }}</span>
                    @endif
                </label>
            </div>
        @empty
            <p class="text-muted mb-0">{{ __('No roles defined.') }}</p>
        @endforelse

        @error('roles')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>

<button type="submit" class="btn btn-primary">
    {{ $editing ? __('Save changes') : __('Create user') }}
</button>
<a href="{{ route('admin.users.index') }}" class="btn btn-link">{{ __('Cancel') }}</a>
