<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\PropietarioController;
use App\Http\Controllers\Api\ConductorController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\DocumentoVehiculoController;
use App\Http\Controllers\Api\DocumentoConductorController;
use App\Http\Controllers\Api\AlertaController;

/*
|--------------------------------------------------------------------------
| API Routes - Control Vehicular
|--------------------------------------------------------------------------
|
| Rutas API protegidas con Laravel Sanctum.
| Todas las rutas requieren autenticación via token Bearer.
|
| Uso:
| 1. POST /api/auth/login para obtener token
| 2. Incluir header: Authorization: Bearer {token}
|
*/

// ============================================================================
// RUTAS PÚBLICAS (sin autenticación)
// ============================================================================

// Health check
Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'API funcionando correctamente',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Autenticación - con rate limiting estricto
Route::middleware('throttle:api-auth')->prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
});

// ============================================================================
// RUTAS PROTEGIDAS (requieren token Sanctum)
// ============================================================================

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // --- Autenticación (usuario autenticado) ---
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user'])->name('api.auth.user');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('api.auth.logout-all');
    });

    // --- Usuarios (solo ADMIN) ---
    Route::middleware('ability:*,usuarios:read')->group(function () {
        Route::get('usuarios', [UsuarioController::class, 'index'])->name('api.usuarios.index');
        Route::get('usuarios/{usuario}', [UsuarioController::class, 'show'])->name('api.usuarios.show');
    });
    Route::middleware(['ability:*', 'throttle:api-write'])->group(function () {
        Route::post('usuarios', [UsuarioController::class, 'store'])->name('api.usuarios.store');
        Route::put('usuarios/{usuario}', [UsuarioController::class, 'update'])->name('api.usuarios.update');
        Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('api.usuarios.destroy');
    });

    // --- Propietarios ---
    Route::middleware('ability:*,propietarios:read')->group(function () {
        Route::get('propietarios', [PropietarioController::class, 'index'])->name('api.propietarios.index');
        Route::get('propietarios/{propietario}', [PropietarioController::class, 'show'])->name('api.propietarios.show');
    });
    Route::middleware(['ability:*,propietarios:write', 'throttle:api-write'])->group(function () {
        Route::post('propietarios', [PropietarioController::class, 'store'])->name('api.propietarios.store');
        Route::put('propietarios/{propietario}', [PropietarioController::class, 'update'])->name('api.propietarios.update');
        Route::delete('propietarios/{propietario}', [PropietarioController::class, 'destroy'])->name('api.propietarios.destroy');
    });

    // --- Conductores ---
    Route::middleware('ability:*,conductores:read')->group(function () {
        Route::get('conductores', [ConductorController::class, 'index'])->name('api.conductores.index');
        Route::get('conductores/{conductor}', [ConductorController::class, 'show'])->name('api.conductores.show');
    });
    Route::middleware(['ability:*,conductores:write', 'throttle:api-write'])->group(function () {
        Route::post('conductores', [ConductorController::class, 'store'])->name('api.conductores.store');
        Route::put('conductores/{conductor}', [ConductorController::class, 'update'])->name('api.conductores.update');
        Route::delete('conductores/{conductor}', [ConductorController::class, 'destroy'])->name('api.conductores.destroy');
    });

    // --- Vehículos ---
    Route::middleware('ability:*,vehiculos:read')->group(function () {
        Route::get('vehiculos', [VehiculoController::class, 'index'])->name('api.vehiculos.index');
        Route::get('vehiculos/{vehiculo}', [VehiculoController::class, 'show'])->name('api.vehiculos.show');
    });
    Route::middleware(['ability:*,vehiculos:write', 'throttle:api-write'])->group(function () {
        Route::post('vehiculos', [VehiculoController::class, 'store'])->name('api.vehiculos.store');
        Route::put('vehiculos/{vehiculo}', [VehiculoController::class, 'update'])->name('api.vehiculos.update');
        Route::delete('vehiculos/{vehiculo}', [VehiculoController::class, 'destroy'])->name('api.vehiculos.destroy');
    });

    // --- Documentos de Vehículo ---
    Route::middleware('ability:*,documentos:read')->group(function () {
        Route::get('documentos-vehiculo', [DocumentoVehiculoController::class, 'index'])->name('api.documentos.vehiculo.index');
        Route::get('documentos-vehiculo/{documentoVehiculo}', [DocumentoVehiculoController::class, 'show'])->name('api.documentos.vehiculo.show');
    });
    Route::middleware(['ability:*,documentos:write', 'throttle:api-write'])->group(function () {
        Route::post('documentos-vehiculo', [DocumentoVehiculoController::class, 'store'])->name('api.documentos.vehiculo.store');
        Route::put('documentos-vehiculo/{documentoVehiculo}', [DocumentoVehiculoController::class, 'update'])->name('api.documentos.vehiculo.update');
        Route::delete('documentos-vehiculo/{documentoVehiculo}', [DocumentoVehiculoController::class, 'destroy'])->name('api.documentos.vehiculo.destroy');
    });

    // --- Documentos de Conductor ---
    Route::middleware('ability:*,documentos:read')->group(function () {
        Route::get('documentos-conductor', [DocumentoConductorController::class, 'index'])->name('api.documentos.conductor.index');
        Route::get('documentos-conductor/{documentoConductor}', [DocumentoConductorController::class, 'show'])->name('api.documentos.conductor.show');
    });
    Route::middleware(['ability:*,documentos:write', 'throttle:api-write'])->group(function () {
        Route::post('documentos-conductor', [DocumentoConductorController::class, 'store'])->name('api.documentos.conductor.store');
        Route::put('documentos-conductor/{documentoConductor}', [DocumentoConductorController::class, 'update'])->name('api.documentos.conductor.update');
        Route::delete('documentos-conductor/{documentoConductor}', [DocumentoConductorController::class, 'destroy'])->name('api.documentos.conductor.destroy');
    });

    // --- Alertas ---
    Route::middleware('ability:*,alertas:read')->group(function () {
        Route::get('alertas', [AlertaController::class, 'index'])->name('api.alertas.index');
        Route::get('alertas/{alerta}', [AlertaController::class, 'show'])->name('api.alertas.show');
    });
    Route::middleware(['ability:*,alertas:write', 'throttle:api-write'])->group(function () {
        Route::post('alertas', [AlertaController::class, 'store'])->name('api.alertas.store');
        Route::put('alertas/{alerta}', [AlertaController::class, 'update'])->name('api.alertas.update');
        Route::delete('alertas/{alerta}', [AlertaController::class, 'destroy'])->name('api.alertas.destroy');
    });
});
