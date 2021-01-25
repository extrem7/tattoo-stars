<?php

namespace Modules\Api\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected $moduleNamespace = 'Modules\Api\Http\Controllers';

    public function map(): void
    {
        $this->mapApiRoutes();
    }

    protected function mapApiRoutes(): void
    {
        Route::domain(env('API_DOMAIN'))
            ->middleware('api')
            ->namespace($this->moduleNamespace)
            ->group(module_path('Api', 'routes.php'));
    }
}
