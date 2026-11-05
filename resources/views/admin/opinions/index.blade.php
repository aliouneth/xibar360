@extends('layouts.admin')

@section('title', __('Opinions'))

@section('content')
<h1>{{ __('Opinions') }}</h1>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{{ __('ID') }}</th>
                <th>{{ __('Content') }}</th>
                <th>{{ __('IP') }}</th>
                <th>{{ __('Date') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($opinions as $opinion)
            <tr>
                <td>{{ $opinion->id }}</td>
                <td style="max-width: 400px; white-space: pre-wrap;">{{ $opinion->content }}</td>
                <td>{{ $opinion->ip_address }}</td>
                <td>{{ $opinion->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                    <form action="{{ route('admin.opinions.destroy', $opinion) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $opinions->links() }}
</div>
@endsection
