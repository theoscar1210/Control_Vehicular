<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    public function boot(): void
    {


        $this->routes(function () {
            // Carga segura de rutas API (prefijo /api) — solo si existe el archivo

            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));


            // Carga segura de rutas web 

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
