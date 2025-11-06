<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Models\Vehiculo;
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

    Route::middleware(['auth'])->group(function () {
        // Rutas protegidas para roles SST o ADMIN
        Route::resource('vehiculos', VehiculoController::class)->except(['show']);
        // Otras rutas específicas para SST o ADMIN pueden ir aquí
    });


    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
