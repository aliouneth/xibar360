<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Schema::defaultStringLength(160);
    }

    public function boot(): void
    {
        if ($this->app->isLocal()) {
            URL::forceScheme('http');
        }

        $locale = session('locale', config('app.locale', 'fr'));
        app()->setLocale($locale);
    }
}
