<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\DocumentoVehiculoController;
use App\Http\Controllers\PropietarioController;
use Illuminate\Support\Facades\Route;

//  Rutas públicas
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//  Rutas protegidas
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.home');

    //  Gestión de usuarios (solo ADMIN)
    Route::middleware(['auth'])->group(function () {
        Route::resource('usuarios', UserController::class)->except(['show']);
    });

    // ================================
    // Módulo de Vehículos
    // ================================

    // Vista principal de gestión de vehículos
    Route::get('vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');

    //formulario de creacion vehiculos - propietarios - documentos
    Route::get('vehiculos/create', [VehiculoController::class, 'create'])->name('vehiculos.create');
    // Guardar vehículo
    Route::post('vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');

    // Guardar propietario (cuando se registre desde el flujo del vehículo)
    Route::post('propietarios', [PropietarioController::class, 'store'])->name('propietarios.store');
    // Guardar documentos del vehículo
    Route::post('vehculos/{id}/documentos', [DocumentoVehiculoController::class, 'store'])->name('documentos.store');

    //editar vehiculo
    Route::get('vehiculos/{id}/edit', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
    Route::put('vehiculos/{id}', [VehiculoController::class, 'update'])->name('vehiculos.update');

    // Eliminar vehículo
    Route::delete('vehiculos/{id}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');


    // ================================
    //  Perfil de usuario
    // ================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
