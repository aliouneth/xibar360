@extends('layouts.admin')

@section('title', __('Admin Dashboard'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h1 class="mb-0">{{ __('Admin Dashboard') }}</h1>

    <div class="d-flex align-items-center gap-3">
        @if(! empty($lastRefresh))
            <span class="text-muted small">
                {{ __('Last refresh') }}: {{ \Carbon\Carbon::parse($lastRefresh['at'])->translatedFormat('d/m/Y H:i') }}
                ({{ $lastRefresh['imported'] }} {{ __('added') }})
            </span>
        @endif

        <form method="POST" action="{{ route('admin.news.refresh') }}" class="d-inline"
              onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').textContent = '{{ __('Refreshing...') }}';">
            @csrf
            <button type="submit" class="btn btn-primary">
                🔄 {{ __('Refresh News') }}
            </button>
        </form>
    </div>
</div>

@if(session('news_report'))
    @php($report = session('news_report'))
    <div class="card mt-3">
        <div class="card-header">{{ __('Import report') }}</div>
        <div class="card-body">
            <p class="mb-2">
                {{ __('Fetched') }}: <strong>{{ $report['total'] }}</strong> &middot;
                {{ __('Imported') }}: <strong>{{ $report['imported'] }}</strong> &middot;
                {{ __('Skipped') }}: <strong>{{ $report['skipped'] }}</strong> &middot;
                {{ __('Duration') }}: <strong>{{ $report['duration'] }}s</strong>
            </p>
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Source') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-end">{{ __('Articles') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['sources'] as $source)
                        <tr>
                            <td>{{ $source['name'] }}</td>
                            <td>
                                @if($source['ok'])
                                    <span class="badge text-bg-success">{{ __('OK') }}</span>
                                @else
                                    <span class="badge text-bg-danger" title="{{ $source['error'] }}">{{ __('Failed') }}</span>
                                @endif
                            </td>
                            <td class="text-end">{{ $source['ok'] ? $source['count'] : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Total Articles') }}</h5>
                <h2>{{ $totalArticles ?? $stats['total_articles'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Published') }}</h5>
                <h2>{{ $publishedArticles ?? $stats['published_articles'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Imported') }}</h5>
                <h2>{{ $importedArticles ?? $stats['imported_articles'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Local') }}</h5>
                <h2>{{ $localArticles ?? $stats['local_articles'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Total Users') }}</h5>
                <h2>{{ $totalUsers ?? $stats['total_users'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Admins') }}</h5>
                <h2>{{ $totalAdmins ?? $stats['total_admins'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Editors') }}</h5>
                <h2>{{ $totalEditors ?? $stats['total_editors'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">{{ __('Active Ads') }}</h5>
                <h2>{{ $activeAds ?? $stats['active_ads'] ?? 0 }}</h2>
            </div>
        </div>
    </div>
</div>
@endsection
