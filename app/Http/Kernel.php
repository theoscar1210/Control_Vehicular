<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * ⚠️  ARCHIVO INACTIVO EN LARAVEL 12
 *
 * En Laravel 12, el registro de middleware se hace en bootstrap/app.php.
 * Este archivo NO es cargado por el framework y sus definiciones NO tienen efecto.
 *
 * Los aliases de middleware están registrados en:
 *   bootstrap/app.php → withMiddleware() → $middleware->alias([...])
 *
 * Este archivo se conserva únicamente como referencia histórica.
 */
class Kernel extends HttpKernel
{
    // Ver bootstrap/app.php para la configuración activa de middleware.
}
