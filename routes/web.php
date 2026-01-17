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

/*
|--------------------------------------------------------------------------
| RUTAS PÚBLICAS
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

    /*
    |--------------------------------------------------------------------------
    | GESTIÓN DE USUARIOS (Solo ADMIN)
    |--------------------------------------------------------------------------
    */
    Route::resource('usuarios', UserController::class)->except(['show']);

    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS
    |--------------------------------------------------------------------------
    */
    Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
        Route::get('/', [VehiculoController::class, 'index'])->name('index');
        Route::get('/create', [VehiculoController::class, 'create'])->name('create');
        Route::post('/', [VehiculoController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [VehiculoController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VehiculoController::class, 'update'])->name('update');
        Route::delete('/{id}', [VehiculoController::class, 'destroy'])->name('destroy');

        /*
        |--------------------------------------------------------------------------
        | DOCUMENTOS DE VEHÍCULOS
        |--------------------------------------------------------------------------
        */
        Route::prefix('{vehiculo}/documentos')->name('documentos.')->group(function () {
            // Crear documento
            Route::post('/', [DocumentoVehiculoController::class, 'store'])->name('store');

            // Editar/Renovar documento específico
            Route::get('/{documento}/edit', [DocumentoVehiculoController::class, 'edit'])->name('edit');
            Route::put('/{documento}', [DocumentoVehiculoController::class, 'update'])->name('update');

            // Historial completo del vehículo (todos los documentos)
            Route::get('/historial', [DocumentoVehiculoController::class, 'historialCompleto'])->name('historial.completo');

            // Historial de un tipo específico (opcional)
            Route::get('/{tipo}/historial', [DocumentoVehiculoController::class, 'historial'])->name('historial');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PROPIETARIOS
    |--------------------------------------------------------------------------
    */
    Route::post('propietarios', [PropietarioController::class, 'store'])->name('propietarios.store');

    /*
    |--------------------------------------------------------------------------
    | CONDUCTORES
    |--------------------------------------------------------------------------
    */
    Route::prefix('conductores')->name('conductores.')->group(function () {
        Route::get('/create', [ConductorController::class, 'create'])->name('create');
        Route::post('/', [ConductorController::class, 'store'])->name('store');
        Route::get('/{conductor}/edit', [ConductorController::class, 'edit'])->name('edit');
        Route::put('/{conductor}', [ConductorController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTOS DE CONDUCTORES
    |--------------------------------------------------------------------------
    */
    Route::resource('documentos_conductor', DocumentoConductorController::class)
        ->only(['store', 'update', 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | CONSULTAS Y REPORTES
    |--------------------------------------------------------------------------
    */
    Route::prefix('consultar-documentos')->name('documentos.consultar')->group(function () {
        Route::get('/', [DocumentoController::class, 'index']);
        Route::get('/export/excel', [DocumentoController::class, 'exportExcel'])->name('.export.excel');
        Route::get('/export/pdf', [DocumentoController::class, 'exportPdf'])->name('.export.pdf');
    });

    Route::get('/documentos_conductor/{documento}/download', [DocumentoController::class, 'download'])
        ->name('documentos_conductor.download');

    /*
    |--------------------------------------------------------------------------
    | PERFIL DE USUARIO
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | ALERTAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('alertas')->name('alertas.')->group(function () {
        Route::get('/', [AlertaController::class, 'index'])->name('index');
        Route::get('/unread-count', [AlertaController::class, 'unreadCount'])->name('unread_count');
        Route::post('/mark-all-read', [AlertaController::class, 'markAllRead'])->name('mark_all_read');
        Route::get('/{alerta}', [AlertaController::class, 'show'])->name('show');
        Route::post('/', [AlertaController::class, 'store'])->name('store');
        Route::post('/{alerta}/read', [AlertaController::class, 'markAsRead'])->name('read');
        Route::delete('/{alerta}', [AlertaController::class, 'destroy'])->name('destroy');
    });
});
