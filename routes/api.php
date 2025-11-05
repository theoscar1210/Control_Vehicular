
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\PropietarioController;
use App\Http\Controllers\Api\ConductorController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\DocumentoVehiculoController;
use App\Http\Controllers\Api\DocumentoConductorController;
use App\Http\Controllers\Api\AlertaController;

/*
API Routes
Rutas API para el proyecto Control_Vehicular
*/

// Ping de verificación
Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// Usuarios API con nombres únicos
Route::apiResource('usuarios', UsuarioController::class)->names([
    'index' => 'api.usuarios.index',
    'store' => 'api.usuarios.store',
    'show' => 'api.usuarios.show',
    'update' => 'api.usuarios.update',
    'destroy' => 'api.usuarios.destroy',
]);

// Otros recursos
Route::apiResource('propietarios', PropietarioController::class)->names('api.propietarios');
Route::apiResource('conductores', ConductorController::class)->names('api.conductores');
Route::apiResource('vehiculos', VehiculoController::class)->names('api.vehiculos');
Route::apiResource('documentos-vehiculo', DocumentoVehiculoController::class)->names('api.documentos.vehiculo');
Route::apiResource('documentos-conductor', DocumentoConductorController::class)->names('api.documentos.conductor');
Route::apiResource('alertas', AlertaController::class)->names('api.alertas');
