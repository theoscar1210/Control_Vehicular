<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{

    //globales
    protected $middleware = [];

    // por grupo

    protected $middlewareGroups = [
        'web' => [],
        'api' => [],
    ];

    //rutas indicviduales


    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        // ... otras entradas ...
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'is.admin' => \App\Http\Middleware\IsAdmin::class,
        // Añadir aquí:
        //'role.exists' => \App\Http\Middleware\EnsureRoleExists::class,
    ];
}
