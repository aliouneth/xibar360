@extends('layouts.admin')

@section('title', __('Users'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0">{{ __('User Management') }}</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ __('Add user') }}</a>
</div>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('Status') }}</th>
                <th class="text-end">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>
                        <a href="{{ route('admin.users.show', $user) }}">{{ $user->name }}</a>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @forelse($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @empty
                            <span class="text-muted small">{{ __('No roles') }}</span>
                        @endforelse
                    </td>
                    <td>
                        @if($user->is(auth()->user()))
                            <span class="text-muted small">{{ __('You cannot change your own status') }}</span>
                        @else
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $user->is_active ? __('Active') : __('Disabled') }}
                                </button>
                            </form>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                            {{ __('Edit') }}
                        </a>
                        @if(! $user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                  class="d-inline"
                                  onsubmit="return confirm('{{ __('Delete this user?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">{{ __('No users yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
