<?php

namespace App\Providers;

use App\Models\Ad;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Schema::defaultStringLength(160);
    }

    public function boot(): void
    {
        // Force HTTPS URLs in production (behind TLS/proxy). Never in local dev,
        // where the dev server is plain http and forcing https breaks assets.
        if (! $this->app->isLocal()) {
            URL::forceScheme('https');
        }

        $locale = session('locale', config('app.locale', 'fr'));
        app()->setLocale($locale);

        // Single query feeding every public ad zone (seneweb-style wrapper +
        // header/sidebar/inline/footer slots), so each page renders ads
        // without controllers re-querying them by hand.
        View::composer(
            ['layouts.app', 'home', 'articles.show', 'public.category', 'search.results'],
            function ($view): void {
                $adsByZone = Ad::active()
                    ->orderByDesc('order')
                    ->orderByDesc('id')
                    ->get()
                    ->groupBy('zone');

                $view->with([
                    'headerLeftAds' => $adsByZone->get('header_left', collect()),
                    'headerCenterAds' => $adsByZone->get('header_center', collect()),
                    'headerRightAds' => $adsByZone->get('header_right', collect()),
                    'sidebarAds' => $adsByZone->get('sidebar', collect()),
                    'inlineAds' => $adsByZone->get('inline', collect()),
                    'footerAds' => $adsByZone->get('footer', collect()),
                    'wrapperAd' => $adsByZone->get('wrapper', collect())->first(),
                ]);
            }
        );
    }
}
