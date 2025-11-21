<?php

use App\Http\Controllers\DocumentoConductorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\DocumentoVehiculoController;
use App\Http\Controllers\PropietarioController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\AlertaController;
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
    Route::post('vehiculos/{id}/documentos', [DocumentoVehiculoController::class, 'store'])->name('documentos.store');
    // Documentos vehiculo

    // Actualizar documentos de un vehículo
    Route::get('vehiculos/{id}/documentos', [DocumentoVehiculoController::class, 'edit'])->name('documentos_vehiculo.edit');
    Route::put('documentos/{id}', [DocumentoVehiculoController::class, 'update'])->name('documentos_vehiculo.update');
    Route::post('documentos/{id}/replace', [DocumentoVehiculoController::class, 'replace'])->name('documentos_vehiculo.replace');
    Route::post('documentos/{id}/mark-replaced', [DocumentoVehiculoController::class, 'markReplaced'])->name('documentos_vehiculo.mark_replaced');



    //editar vehiculo
    Route::get('vehiculos/{id}/edit', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
    Route::put('vehiculos/{id}', [VehiculoController::class, 'update'])->name('vehiculos.update');

    // Eliminar vehículo
    Route::delete('vehiculos/{id}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // ================================
    // Conductores y Documentos (licencias)
    // ================================
    // Conductores (creación y visualización mínima)
    Route::get('/conductores/create', [ConductorController::class, 'create'])->name('conductores.create');
    Route::post('/conductores', [ConductorController::class, 'store'])->name('conductores.store');
    Route::get('/conductores/{conductor}/edit', [ConductorController::class, 'edit'])->name('conductores.edit');
    Route::put('/conductores/{conductor}', [ConductorController::class, 'update'])->name('conductores.update');
    Route::resource('documentos_conductor', DocumentoConductorController::class)->only(['store', 'update', 'destroy']);
    //=====================================
    //Consultas y reporter
    //=====================================

    // Vista de consulta (única vista central)

    Route::get('/consultar-documentos', [DocumentoController::class, 'index'])
        ->name('documentos.consultar');

    // Exports (mismos filtros que index, devuelven archivo)
    Route::get('/consultar-documentos/export/excel', [DocumentoController::class, 'exportExcel'])
        ->name('documentos.consultar.export.excel');

    Route::get('/consultar-documentos/export/pdf', [DocumentoController::class, 'exportPdf'])
        ->name('documentos.consultar.export.pdf');

    // Descargar archivo asociado a un documento (si existe ruta_archivo)
    Route::get('/documentos_conductor/{documento}/download', [DocumentoController::class, 'download'])
        ->name('documentos_conductor.download');
    // ================================
    //  Perfil de usuario
    // ================================
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::middleware(['auth'])->group(function () {
        Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
        Route::get('alertas/{alerta}', [AlertaController::class, 'show'])->name('alertas.show');
        Route::post('alertas', [AlertaController::class, 'store'])->name('alertas.store'); // para crear manual
        Route::delete('alertas/{alerta}', [AlertaController::class, 'destroy'])->name('alertas.destroy');

        // acciones AJAX / helpers
        Route::post('alertas/{alerta}/read', [AlertaController::class, 'markAsRead'])->name('alertas.read');
        Route::post('alertas/mark-all-read', [AlertaController::class, 'markAllRead'])->name('alertas.mark_all_read');
        Route::get('alertas/unread-count', [AlertaController::class, 'unreadCount'])->name('alertas.unread_count');
    });
});
