@extends('layouts.admin')

@section('title', __('News Sources'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <h1 class="mb-0">{{ __('News Sources') }}</h1>

    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.sources.create') }}" class="btn btn-primary">{{ __('Add Source') }}</a>

        <form method="POST" action="{{ route('admin.news.refresh') }}" class="d-inline"
              onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = '{{ __('Refreshing...') }}';">
            @csrf
            <button type="submit" class="btn btn-outline-primary">{{ __('Refresh News') }}</button>
        </form>
    </div>
</div>

<p class="text-muted">
    {{ __('Enabled sources are polled by the news refresh. Disable a source to skip it without deleting it.') }}
</p>

<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Feed URL') }}</th>
                <th>{{ __('Language') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Last checked') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sources as $source)
                <tr>
                    <td>{{ $source->name }}</td>
                    <td>
                        <a href="{{ $source->url }}" target="_blank" rel="noopener">
                            {{ \Illuminate\Support\Str::limit($source->url, 60) }}
                        </a>
                    </td>
                    <td>{{ strtoupper($source->language) }}</td>
                    <td>
                        @if($source->enabled)
                            <span class="badge text-bg-success">{{ __('Enabled') }}</span>
                        @else
                            <span class="badge text-bg-secondary">{{ __('Disabled') }}</span>
                        @endif

                        @if($source->last_status === 'failed')
                            <span class="badge text-bg-danger" title="{{ $source->last_error }}">{{ __('Last scan failed') }}</span>
                        @elseif($source->last_status === 'ok')
                            <span class="badge text-bg-light text-dark">{{ __('Last scan ok') }}</span>
                        @endif
                    </td>
                    <td class="small text-muted">
                        {{ $source->last_checked_at ? $source->last_checked_at->format('d/m/Y H:i') : '—' }}
                    </td>
                    <td class="text-nowrap">
                        <a href="{{ route('admin.sources.edit', $source) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit') }}</a>

                        <form action="{{ route('admin.sources.toggle', $source) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                {{ $source->enabled ? __('Disable') : __('Enable') }}
                            </button>
                        </form>

                        <form action="{{ route('admin.sources.destroy', $source) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('{{ __('Delete this source? Imported articles are kept.') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">{{ __('No sources yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $sources->links() }}
</div>
@endsection
