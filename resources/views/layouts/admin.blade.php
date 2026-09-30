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
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Xibar360" class="h-[60px] w-auto" style="height: 60px !important;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav ms-auto flex-column flex-md-row align-items-center gap-2">
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.stats') }}">{{ __('Stats') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.articles.index') }}">{{ __('Articles') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.categories.index') }}">{{ __('Categories') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.ads.index') }}">{{ __('Ads') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">{{ __('Users') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('admin.settings.index') }}">{{ __('Settings') }}</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('View Site') }}</a></li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link p-0">{{ __('Logout') }}</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
