<?php

namespace Modules\Admin\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Modules\Admin\Http\Middleware\EnableDebugBar;
use Modules\Admin\Http\Middleware\HandleInertiaRequests;

class RouteServiceProvider extends ServiceProvider
{

    protected string $moduleNamespace = 'Modules\Admin\Http\Controllers';

    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Admin', '/routes/api.php'));
    }

    protected function mapWebRoutes(): void
    {
        Route::domain(env('ADMIN_DOMAIN'))
            ->middleware(['web', EnableDebugBar::class, HandleInertiaRequests::class])
            ->as('admin.')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Admin', '/routes/web.php'));
    }
}
