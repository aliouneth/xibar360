@extends('layouts.admin')

@section('title', __('Ads'))

@section('content')
<h1>{{ __('Ads Management') }}</h1>
<a href="{{ route('admin.ads.create') }}" class="btn btn-primary mb-3">{{ __('Create Ad') }}</a>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Zone') }}</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ads as $ad)
            <tr>
                <td>{{ $ad->id }}</td>
                <td>{{ $ad->title }}</td>
                <td>{{ $ad->zone }}</td>
                <td>
                    <form action="{{ route('admin.ads.toggle', $ad) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm {{ $ad->is_active ? 'btn-success' : 'btn-secondary' }}">
                            {{ $ad->is_active ? 'Yes' : 'No' }}
                        </button>
                    </form>
                </td>
                <td>
                    <a href="{{ route('admin.ads.edit', $ad) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $ads->links() }}
</div>
@endsection
