<?php

namespace App\Http;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends BaseServiceProvider
{
    public const HOME = '/home';

    public function map(): void
    {
        $this->mapWebRoutes();
        $this->mapAdminRoutes();
        $this->mapEditorRoutes();
        $this->mapApiRoutes();
    }

    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapAdminRoutes(): void
    {
        Route::middleware(['web', 'auth', 'role:admin'])
            ->prefix('admin')
            ->namespace($this->namespace . '\\Admin')
            ->group(base_path('routes/admin.php'));
    }

    protected function mapEditorRoutes(): void
    {
        Route::middleware(['web', 'auth', 'role:admin|editor'])
            ->prefix('editor')
            ->namespace($this->namespace . '\\Editor')
            ->group(base_path('routes/editor.php'));
    }

    protected function mapApiRoutes(): void
    {
        Route::middleware('api')
            ->prefix('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
