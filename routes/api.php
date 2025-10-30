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
Rutas API para el proyecto Controñ_Vehicular

*/

//ping de verificacion


Route::get('/ping', function () {
    return response()->json(['message' => 'API funcionando correctamente']);
});

// Recursos principales
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('propietarios', PropietarioController::class);
Route::apiResource('conductores', ConductorController::class);
Route::apiResource('vehiculos', VehiculoController::class);
Route::apiResource('documentos-vehiculo', DocumentoVehiculoController::class);
Route::apiResource('documentos-conductor', DocumentoConductorController::class);
Route::apiResource('alertas', AlertaController::class);
