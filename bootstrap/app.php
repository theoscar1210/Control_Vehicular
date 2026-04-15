<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Carbon\Carbon;

// Configurar Carbon para español
Carbon::setLocale('es');

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        // Aplicar NoCacheHeaders a todas las rutas API (previene caché de respuestas con datos sensibles)
        $middleware->api(append: [
            \App\Http\Middleware\NoCacheHeaders::class,
        ]);

        $middleware->alias([
            'auth'         => \App\Http\Middleware\Authenticate::class,
            'role'         => \App\Http\Middleware\CheckRole::class,
            'nocache'      => \App\Http\Middleware\NoCacheHeaders::class,
            'ability'      => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
            'abilities'    => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
            // Middleware de roles adicionales (registrados aquí — Kernel.php es inactivo en Laravel 12)
            'is.admin'     => \App\Http\Middleware\IsAdmin::class,
            'role.sst.admin' => \App\Http\Middleware\SstOrAdmin::class,
            'ensure.role'  => \App\Http\Middleware\EnsureRoleExists::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
