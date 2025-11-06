<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    //globales
    protected $middleware = [];

    // por grupo

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        'api' => [],
    ];

    //rutas indicviduales


    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // ... otras entradas ...
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'is.admin' => \App\Http\Middleware\IsAdmin::class,


        'role.sst.admin' => \App\Http\Middleware\SstOrAdmin::class,
        // Añadir aquí:
        //'role.exists' => \App\Http\Middleware\EnsureRoleExists::class,
    ];
}
