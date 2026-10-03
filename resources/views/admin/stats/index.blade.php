@extends('layouts.admin')

@section('title', __('Site Statistics'))

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <h1 class="mb-0">{{ __('Site Statistics') }}</h1>
    <div class="btn-group btn-group-sm" role="group" aria-label="{{ __('Range') }}">
        @foreach([7, 14, 30, 90] as $range)
            <a href="{{ route('admin.stats', ['days' => $range]) }}"
               class="btn {{ $days === $range ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $range }}j
            </a>
        @endforeach
    </div>
</div>

{{-- Visitors: public audience, tracked per page view --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-semibold">👥 {{ __('Visitors') }}</span>
        <span class="small text-muted">
            {{ __('Last') }} {{ $days }}{{ __('d') }} &middot;
            {{ __('since') }} {{ $visitorSince }}
        </span>
    </div>
    <div class="card-body">
        @if(! $hasVisitorData)
            <p class="text-muted mb-0">
                {{ __('No traffic recorded yet. Figures appear here as soon as visitors browse the public site.') }}
            </p>
        @else
            <div class="row g-3 mb-4">
                @foreach($visitorCards as $card)
                    <div class="col-6 col-md-4 col-xl">
                        <div class="border rounded p-3 h-100 bg-light bg-opacity-50">
                            <div class="text-muted small text-uppercase">{{ $card['label'] }}</div>
                            <div class="fs-4 fw-bold">{{ $card['value'] }}</div>
                            <div class="text-muted small">{{ $card['sub'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row g-4">
                <div class="col-12">
                    <h6 class="text-muted text-uppercase small mb-2">{{ __('Daily audience') }}</h6>
                    <div class="d-flex align-items-end gap-1" style="height: 110px;">
                        @foreach($visitorTrend as $point)
                            <div class="flex-fill d-flex flex-column justify-content-end align-items-center gap-1"
                                 title="{{ $point['label'] }}: {{ $point['views'] }} {{ __('views') }}, {{ $point['visitors'] }} {{ __('visitors') }}">
                                <span class="small text-muted">{{ $point['views'] > 0 ? $point['visitors'] : '' }}</span>
                                <div class="w-100 bg-secondary rounded-top" style="height: {{ $point['height'] }}%;"></div>
                                <span class="small text-muted">{{ $point['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="small text-muted mt-2">{{ __('Bar height = page views, number above = unique visitors') }}</div>
                </div>

                <div class="col-lg-6">
                    <h6 class="text-muted text-uppercase small mb-2">{{ __('Most read articles') }}</h6>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Article') }}</th>
                                    <th class="text-end">{{ __('Views') }}</th>
                                    <th class="text-end">{{ __('Visitors') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topViewedArticles as $row)
                                    <tr>
                                        <td>
                                            <a href="{{ $row['url'] }}" class="text-decoration-none">{{ \Illuminate\Support\Str::limit($row['title'], 62) }}</a>
                                            @if($row['source'])
                                                <div class="small text-muted">{{ $row['source'] }}</div>
                                            @endif
                                        </td>
                                        <td class="text-end fw-semibold">{{ number_format($row['views']) }}</td>
                                        <td class="text-end text-muted">{{ number_format($row['visitors']) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">{{ __('No article views yet.') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <h6 class="text-muted text-uppercase small mb-2">{{ __('Top referrers') }}</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($topReferrers as $row)
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span class="text-truncate">{{ $row['host'] }}</span>
                                        <span class="badge text-bg-light text-dark">{{ number_format($row['views']) }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted px-0">{{ __('No referrers recorded.') }}</li>
                                @endforelse
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <h6 class="text-muted text-uppercase small mb-2">{{ __('Devices') }}</h6>
                            <ul class="list-group list-group-flush">
                                @forelse($deviceSplit as $row)
                                    <li class="list-group-item d-flex justify-content-between px-0">
                                        <span>{{ ucfirst((string) $row['device']) }} ({{ $row['pct'] }}%)</span>
                                        <span class="badge text-bg-light text-dark">{{ number_format($row['views']) }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item text-muted px-0">{{ __('No data yet.') }}</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <h6 class="text-muted text-uppercase small mb-2 mt-3">{{ __('Top pages') }}</h6>
                    <ul class="list-group list-group-flush">
                        @forelse($topPaths as $row)
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <code class="text-truncate">{{ $row['path'] }}</code>
                                <span class="badge text-bg-light text-dark">{{ number_format($row['views']) }}</span>
                            </li>
                        @empty
                            <li class="list-group-item text-muted px-0">{{ __('No page views yet.') }}</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Headline totals --}}
<div class="row g-3 mb-4">
    @foreach($totalCards as $card)
        <div class="col-6 col-md-4 col-xl-2">
            <div class="card h-100">
                <div class="card-body py-3">
                    <div class="text-muted small text-uppercase">{{ $card['label'] }}</div>
                    <div class="fs-3 fw-bold">{{ number_format($card['value']) }}</div>
                    <div class="text-muted small">{{ $card['sub'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    {{-- Publication trend --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('Publication trend') }} ({{ $days }}j)</span>
                <span class="small text-muted">
                    {{ number_format(collect($trend)->sum('total')) }} {{ __('articles in this window') }}
                </span>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-end gap-1" style="height: 140px;">
                    @foreach($trend as $point)
                        <div class="flex-fill d-flex flex-column justify-content-end align-items-center gap-1"
                             title="{{ $point['label'] }}: {{ $point['total'] }} ({{ $point['imported'] }} {{ __('imported') }})">
                            <span class="small text-muted">{{ $point['total'] > 0 ? $point['total'] : '' }}</span>
                            <div class="w-100 bg-secondary rounded-top"
                                 style="height: {{ $point['pct'] }}%; background-color: rgba(0,0,0,.08) !important;">
                                <div class="w-100 bg-senegal-red rounded-top" style="height: {{ $point['importedPct'] }}%;"></div>
                            </div>
                            <span class="small text-muted">{{ $point['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="small text-muted mt-2">
                    <span class="badge bg-secondary">&nbsp;</span> {{ __('imported') }}
                    <span class="badge bg-light text-dark border ms-2">&nbsp;</span> {{ __('local') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Sources --}}
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">{{ __('Articles by source') }}</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('Source') }}</th>
                            <th class="text-end">{{ __('Articles') }}</th>
                            <th class="text-end d-none d-sm-table-cell">{{ __('Newest') }}</th>
                            <th style="width: 30%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bySource as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row['total']) }}</td>
                                <td class="text-end text-muted small d-none d-sm-table-cell">
                                    {{ $row['last_seen'] ? \Carbon\Carbon::parse($row['last_seen'])->translatedFormat('d/m/Y') : '—' }}
                                </td>
                                <td>
                                    @php($maxSource = max(1, $bySource->max('total')))
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-senegal-red"
                                             style="width: {{ round($row['total'] / $maxSource * 100) }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">{{ __('No articles yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Categories --}}
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">{{ __('Articles by category') }}</div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('Category') }}</th>
                            <th class="text-end">{{ __('Total') }}</th>
                            <th class="text-end">{{ __('Live') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byCategory as $row)
                            <tr>
                                <td>{{ $row['name'] }}</td>
                                <td class="text-end fw-semibold">{{ number_format($row['total']) }}</td>
                                <td class="text-end text-muted">{{ number_format($row['published']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">{{ __('No categories yet.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Source health --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('Configured feeds') }}</span>
                @if($lastRefresh)
                    <span class="small text-muted">
                        {{ __('Last refresh') }}:
                        {{ \Carbon\Carbon::parse($lastRefresh['at'])->translatedFormat('d/m/Y H:i') }}
                        ({{ $lastRefresh['imported'] }} {{ __('added') }})
                    </span>
                @else
                    <span class="small text-muted">{{ __('Never refreshed') }}</span>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>{{ __('Feed') }}</th>
                            <th class="text-end">{{ __('Stored') }}</th>
                            <th class="text-end d-none d-md-table-cell">{{ __('Newest article') }}</th>
                            <th class="text-end">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sourceHealth as $feed)
                            <tr>
                                <td>{{ $feed['name'] }}</td>
                                <td class="text-end fw-semibold">{{ number_format($feed['stored']) }}</td>
                                <td class="text-end text-muted small d-none d-md-table-cell">
                                    {{ $feed['last_seen'] ? \Carbon\Carbon::parse($feed['last_seen'])->translatedFormat('d/m/Y H:i') : '—' }}
                                </td>
                                <td class="text-end">
                                    @if(! $feed['enabled'])
                                        <span class="badge text-bg-secondary">{{ __('Disabled') }}</span>
                                    @elseif($feed['stored'] === 0)
                                        <span class="badge text-bg-danger">{{ __('No articles') }}</span>
                                    @else
                                        <span class="badge text-bg-success">{{ __('OK') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Roles / authors / ads / languages --}}
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header">{{ __('Users by role') }}</div>
            <ul class="list-group list-group-flush">
                @forelse($byRole as $row)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $row['name'] }}</span>
                        <span class="badge text-bg-light text-dark">{{ $row['total'] }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('No roles yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header">{{ __('Local articles by author') }}</div>
            <ul class="list-group list-group-flush">
                @forelse($topAuthors as $row)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $row['name'] }}</span>
                        <span class="badge text-bg-light text-dark">{{ $row['total'] }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('No local articles yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header">{{ __('Ads by zone') }}</div>
            <ul class="list-group list-group-flush">
                @forelse($ads as $row)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ ucfirst($row['zone']) }}</span>
                        <span>
                            <span class="badge text-bg-success">{{ $row['active'] }}</span>
                            <span class="badge text-bg-light text-dark">{{ $row['total'] }}</span>
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('No ads yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header">{{ __('Articles by language') }}</div>
            <ul class="list-group list-group-flush">
                @forelse($byLanguage as $language => $total)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $language === 'en' ? 'English' : 'Français' }}</span>
                        <span class="badge text-bg-light text-dark">{{ number_format($total) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('No articles yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Geographic stats --}}
    <div class="col-md-6 col-xl-3">
        <div class="card h-100">
            <div class="card-header">{{ __('Top countries') }}</div>
            <ul class="list-group list-group-flush">
                @forelse($countries as $row)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ strtoupper($row['country']) }}</span>
                        <span class="badge text-bg-primary">{{ number_format($row['views']) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">{{ __('No geographic data yet.') }}</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
