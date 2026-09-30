<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="refresh" content="300">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <title>{{ config('app.name', 'Xibar360') }} - @yield('title', '')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root { --senegal-red: #DC143C; --senegal-green: #228B22; --senegal-yellow: #FFD700; --senegal-dark: #1a1a2e; }
        body { font-family: 'Instrument Sans', system-ui, sans-serif; background: #f5f5f5; }
        .senegal-red { color: var(--senegal-red); }
        .bg-senegal-red { background-color: var(--senegal-red); }
        .bg-senegal-green { background-color: var(--senegal-green); }
        .bg-senegal-yellow { background-color: var(--senegal-yellow); }
        .text-senegal-red { color: var(--senegal-red); }
        .text-senegal-green { color: var(--senegal-green); }
        .hover\:shadow-lg:hover { box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card-hover { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.15); }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; bottom: -2px; left: 0; width: 0; height: 2px; background: var(--senegal-red); transition: width 0.3s; }
        .nav-link:hover::after { width: 100%; }
        @media (max-width: 768px) { .mobile-hide { display: none; } }
        @media (min-width: 769px) { .desktop-hide { display: none; } }
        .hero-gradient { background: linear-gradient(135deg, #DC143C 0%, #ff6b6b 50%, #228B22 100%); }
        .category-badge { font-size: 0.75rem; padding: 0.25rem 0.75rem; border-radius: 9999px; font-weight: 600; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    {{-- Header --}}
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="Xibar360" class="h-[5.8126359375rem] w-auto">
                </a>
                
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Accueil</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Politique</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Économie</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Sport</a>
                    <a href="#" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Afrique</a>
                </nav>
                
                <div class="flex items-center space-x-3">
                    <form action="{{ route('search.results') }}" method="GET" class="hidden sm:flex">
                        <input type="text" name="q" placeholder="Rechercher..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm w-48">
                        <button type="submit" class="bg-senegal-red text-white px-3 py-2 rounded-lg ml-1 text-sm">🔍</button>
                    </form>
                    
                    <div class="flex items-center border rounded-lg overflow-hidden">
                        <a href="{{ route('lang.switch', ['locale' => 'fr']) }}" class="px-3 py-2 text-sm font-bold {{ app()->getLocale() === 'fr' ? 'bg-senegal-red text-white' : 'bg-white text-gray-700' }}">FR</a>
                        <div class="w-px h-6 bg-gray-300"></div>
                        <a href="{{ route('lang.switch', ['locale' => 'en']) }}" class="px-3 py-2 text-sm font-bold {{ app()->getLocale() === 'en' ? 'bg-senegal-red text-white' : 'bg-white text-gray-700' }}">EN</a>
                    </div>
                    
                    @auth
                        <a href="{{ url('/admin') }}" class="text-sm font-medium text-gray-700 hover:text-senegal-red">Admin</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-700 hover:text-senegal-red">Déconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-senegal-red">Connexion</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Header Ad Zone --}}
    @yield('header_ad')

    {{-- Main Content --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-senegal-dark text-white py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-3">Xibar360</h3>
                    <p class="text-gray-400 text-sm">Votre source d'information privilégiée pour l'actualité sénégalaise et internationale.</p>
                </div>
                <div>
                    <h4 class="font-bold mb-3">Catégories</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-senegal-yellow transition-colors">Politique</a></li>
                        <li><a href="#" class="hover:text-senegal-yellow transition-colors">Économie</a></li>
                        <li><a href="#" class="hover:text-senegal-yellow transition-colors">Sport</a></li>
                        <li><a href="#" class="hover:text-senegal-yellow transition-colors">Afrique</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-3">Réseaux sociaux</h4>
                    <div class="flex space-x-3 text-gray-400">
                        <span class="hover:text-senegal-yellow cursor-pointer">Facebook</span>
                        <span class="hover:text-senegal-yellow cursor-pointer">Twitter</span>
                        <span class="hover:text-senegal-yellow cursor-pointer">Instagram</span>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-3">Newsletter</h4>
                    <p class="text-sm text-gray-400 mb-2">Recevez nos dernières actualités.</p>
                    <form class="flex">
                        <input type="email" placeholder="Votre email" class="px-3 py-2 rounded-l-lg text-sm flex-grow text-black">
                        <button class="bg-senegal-red px-4 py-2 rounded-r-lg text-sm font-bold">✓</button>
                    </form>
                </div>
            </div>
            <div class="border-t border-gray-700 pt-4 text-center text-sm text-gray-400">
                <p>&copy; 2026 Xibar360. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>