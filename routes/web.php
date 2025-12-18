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
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {


    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');


    // Gestión de usuarios (solo ADMIN)
    Route::middleware(['auth'])->group(function () {
        Route::resource('usuarios', UserController::class)->except(['show']);
    });

    // Vehículos
    Route::get('vehiculos', [VehiculoController::class, 'index'])->name('vehiculos.index');
    Route::get('vehiculos/create', [VehiculoController::class, 'create'])->name('vehiculos.create');
    Route::post('vehiculos', [VehiculoController::class, 'store'])->name('vehiculos.store');
    Route::get('vehiculos/{id}/edit', [VehiculoController::class, 'edit'])->name('vehiculos.edit');
    Route::put('vehiculos/{id}', [VehiculoController::class, 'update'])->name('vehiculos.update');
    Route::delete('vehiculos/{id}', [VehiculoController::class, 'destroy'])->name('vehiculos.destroy');

    // Propietarios
    Route::post('propietarios', [PropietarioController::class, 'store'])->name('propietarios.store');

    // Documentos vehículo
    Route::post('vehiculos/{id}/documentos', [DocumentoVehiculoController::class, 'store'])->name('documentos.store'); // Crear documento para vehículo
    // Renovar documento
    Route::put('/vehiculos/{vehiculo}/documentos/{documento}', [DocumentoVehiculoController::class, 'update'])->name('vehiculos.documentos.update');

    // Formulario de renovación
    Route::get('/vehiculos/{vehiculo}/documentos/{documento}/edit', [DocumentoVehiculoController::class, 'edit'])->name('vehiculos.documentos.edit');
    // Historial de versiones
    Route::get('/vehiculos/{vehiculo}/documentos/{tipo}/historial', [DocumentoVehiculoController::class, 'historial'])->name('vehiculos.documentos.historial');

    // Conductores
    Route::get('/conductores/create', [ConductorController::class, 'create'])->name('conductores.create');
    Route::post('/conductores', [ConductorController::class, 'store'])->name('conductores.store');
    Route::get('/conductores/{conductor}/edit', [ConductorController::class, 'edit'])->name('conductores.edit');
    Route::put('/conductores/{conductor}', [ConductorController::class, 'update'])->name('conductores.update');
    Route::resource('documentos_conductor', DocumentoConductorController::class)->only(['store', 'update', 'destroy']);

    // Consultas y reportes
    Route::get('/consultar-documentos', [DocumentoController::class, 'index'])->name('documentos.consultar');
    Route::get('/consultar-documentos/export/excel', [DocumentoController::class, 'exportExcel'])->name('documentos.consultar.export.excel');
    Route::get('/consultar-documentos/export/pdf', [DocumentoController::class, 'exportPdf'])->name('documentos.consultar.export.pdf');
    Route::get('/documentos_conductor/{documento}/download', [DocumentoController::class, 'download'])->name('documentos_conductor.download');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Alertas
    Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('alertas/{alerta}', [AlertaController::class, 'show'])->name('alertas.show');
    Route::post('alertas', [AlertaController::class, 'store'])->name('alertas.store');
    Route::delete('alertas/{alerta}', [AlertaController::class, 'destroy'])->name('alertas.destroy');
    Route::post('alertas/{alerta}/read', [AlertaController::class, 'markAsRead'])->name('alertas.read');
    Route::post('alertas/mark-all-read', [AlertaController::class, 'markAllRead'])->name('alertas.mark_all_read');
    Route::get('alertas/unread-count', [AlertaController::class, 'unreadCount'])->name('alertas.unread_count');
});
