@extends('layouts.admin')

@section('title', $user->name)

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">{{ $user->name }}</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-link">{{ __('Back to users') }}</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">{{ __('Email') }}</dt>
            <dd class="col-sm-9">{{ $user->email }}</dd>

            <dt class="col-sm-3">{{ __('Status') }}</dt>
            <dd class="col-sm-9">
                <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $user->is_active ? __('Active') : __('Disabled') }}
                </span>
            </dd>

            <dt class="col-sm-3">{{ __('Roles') }}</dt>
            <dd class="col-sm-9">
                @forelse($user->roles as $role)
                    <span class="badge bg-primary">{{ $role->name }}</span>
                @empty
                    <span class="text-muted">{{ __('No roles') }}</span>
                @endforelse
            </dd>

            <dt class="col-sm-3">{{ __('Created') }}</dt>
            <dd class="col-sm-9">{{ $user->created_at?->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>
</div>

<a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">{{ __('Edit') }}</a>
@endsection
