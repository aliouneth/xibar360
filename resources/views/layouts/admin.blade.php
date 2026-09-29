<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name'))</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Xibar360" class="h-[60px] w-auto" style="height: 60px !important;">
            </a>
            <div class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
                <a class="nav-link" href="{{ route('admin.stats') }}">{{ __('Stats') }}</a>
                <a class="nav-link" href="{{ route('admin.articles.index') }}">{{ __('Articles') }}</a>
                <a class="nav-link" href="{{ route('admin.categories.index') }}">{{ __('Categories') }}</a>
                <a class="nav-link" href="{{ route('admin.ads.index') }}">{{ __('Ads') }}</a>
                <a class="nav-link" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a>
                <a class="nav-link" href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a>
                <a class="nav-link" href="{{ route('home') }}">{{ __('View Site') }}</a>
                {{-- logout is registered as POST only, so it must be a form --}}
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link nav-link">{{ __('Logout') }}</button>
                </form>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                <p class="mb-2 fw-semibold">
                    {{ __('Please fix the following:') }}
                </p>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</body>
</html>
