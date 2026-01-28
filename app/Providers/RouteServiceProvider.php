<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->configureRateLimiting();

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

    protected function configureRateLimiting(): void
    {
        // Rate limit general para API: 60 peticiones por minuto
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id_usuario ?: $request->ip());
        });

        // Rate limit estricto para autenticación API: 5 intentos por minuto
        RateLimiter::for('api-auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Rate limit para operaciones de escritura: 30 por minuto
        RateLimiter::for('api-write', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id_usuario ?: $request->ip());
        });
    }
}
