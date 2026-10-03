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
                    <a href="{{ route('category.show', ['category' => 'POLITIQUE']) }}" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Politique</a>
                    <a href="{{ route('category.show', ['category' => 'ÉCONOMIE']) }}" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Économie</a>
                    <a href="{{ route('category.show', ['category' => 'SPORT']) }}" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Sport</a>
                    <a href="{{ route('category.show', ['category' => 'AFRIQUE']) }}" class="nav-link text-gray-700 hover:text-senegal-red font-medium transition-colors">Afrique</a>
                </nav>
                
                <div class="flex items-center space-x-3">
                    <form action="{{ route('search.results') }}" method="GET" class="hidden sm:flex">
                        <input type="text" name="q" placeholder="Rechercher..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-senegal-red text-sm w-48">
                        <button type="submit" class="bg-senegal-red text-white px-3 py-2 rounded-lg ml-1 text-sm">🔍</button>
                    </form>
                    
                    <div class="flex items-center border rounded-lg overflow-hidden">
                        <a href="{{ route('lang.switch', ['locale' => 'fr']) }}" class="px-3 py-2 text-sm font-bold {{ app()->getLocale() === 'fr' ? 'bg-senegal-red text-white' : 'bg-white text-gray-700' }}">FR</a>
                        <div class="w-px h-6 bg-gray-300"></div>
                        <button type="button" data-xibar360-translate="ar" class="px-3 py-2 text-sm font-bold bg-white text-gray-700 hover:bg-gray-100" title="Traduire cette page en arabe dans votre navigateur">عربي</button>
                        <div class="w-px h-6 bg-gray-300"></div>
                        <button type="button" data-xibar360-translate="en" class="px-3 py-2 text-sm font-bold bg-white text-gray-700 hover:bg-gray-100" title="Traduire cette page en anglais dans votre navigateur">EN</button>
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

    <script>
        (function () {
            var buttons = document.querySelectorAll('[data-xibar360-translate]');

            if (!buttons.length) {
                return;
            }

            function notice(message) {
                var box = document.createElement('div');
                box.setAttribute('role', 'status');
                box.textContent = message;
                box.style.cssText = 'position:fixed;z-index:9999;left:50%;bottom:1.5rem;transform:translateX(-50%);max-width:90vw;padding:.75rem 1rem;border-radius:.5rem;background:#1a1a2e;color:#fff;font-size:.875rem;line-height:1.4;box-shadow:0 10px 25px rgba(0,0,0,.2);';
                document.body.appendChild(box);
                window.setTimeout(function () { box.remove(); }, 9000);
            }

            function setBusy(target, busy) {
                var list = document.querySelectorAll('[data-xibar360-translate="' + target + '"]');

                for (var i = 0; i < list.length; i++) {
                    list[i].classList.toggle('opacity-50', busy);
                    list[i].classList.toggle('pointer-events-none', busy);
                }
            }

            function translate(target) {
                if (window.__xibar360Translated) {
                    window.location.reload();
                    return;
                }

                var mount = document.getElementById('xibar360-google-translate');

                if (!mount) {
                    mount = document.createElement('div');
                    mount.id = 'xibar360-google-translate';
                    mount.style.cssText = 'position:fixed;left:-9999px;top:-9999px;';
                    document.body.appendChild(mount);
                }

                window.__xibar360Translated = target;
                setBusy(target, true);
                notice('Traduction en cours...');

                function apply() {
                    var select = mount.querySelector('.goog-te-combo');

                    if (!select) {
                        return false;
                    }

                    select.value = target;
                    select.dispatchEvent(new Event('change', { bubbles: true }));
                    return true;
                }

                window.googleTranslateElementInit = function () {
                    new google.translate.TranslateElement({
                        pageLanguage: document.documentElement.getAttribute('lang') || 'fr',
                        includedLanguages: 'ar,en',
                        autoDisplay: false,
                    }, 'xibar360-google-translate');

                    var attempts = 0;
                    var timer = window.setInterval(function () {
                        attempts += 1;

                        if (apply()) {
                            window.clearInterval(timer);
                            setBusy(target, false);
                            document.documentElement.setAttribute('lang', target);
                            document.documentElement.setAttribute('dir', target === 'ar' ? 'rtl' : 'ltr');
                            notice('Page traduite en ' + (target === 'ar' ? 'arabe' : 'anglais') + '.');
                            return;
                        }

                        if (attempts > 20) {
                            window.clearInterval(timer);
                            setBusy(target, false);
                            document.documentElement.setAttribute('lang', target);
                            document.documentElement.setAttribute('dir', target === 'ar' ? 'rtl' : 'ltr');
                            notice('Utilisez la traduction de votre navigateur.');
                        }
                    }, 250);
                };

                var script = document.createElement('script');
                script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
                script.async = true;
                script.onerror = function () {
                    setBusy(target, false);
                    notice('Traduction Google indisponible. Utilisez la traduction de votre navigateur.');
                };
                document.head.appendChild(script);
            }

            for (var i = 0; i < buttons.length; i++) {
                buttons[i].addEventListener('click', function () {
                    translate(this.getAttribute('data-xibar360-translate'));
                });
            }
        })();
    </script>

    @yield('scripts')
</body>
</html>