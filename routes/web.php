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
use App\Http\Controllers\PorteriaController;
use App\Http\Controllers\ReporteController;
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
Route::middleware(['auth', 'nocache'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PORTERÍA - Acceso: ADMIN, PORTERIA (SST NO tiene acceso)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:ADMIN,PORTERIA'])->group(function () {
        Route::get('/porteria', [PorteriaController::class, 'index'])->name('porteria.index');
    });

    /*
    |--------------------------------------------------------------------------
    | ALERTAS - Acceso: ADMIN, SST, PORTERIA (ver y marcar leídas)
    |--------------------------------------------------------------------------
    */
    Route::prefix('alertas')->name('alertas.')->group(function () {
        Route::get('/', [AlertaController::class, 'index'])->name('index');
        Route::get('/unread-count', [AlertaController::class, 'unreadCount'])->name('unread_count');
        Route::post('/mark-all-read', [AlertaController::class, 'markAllRead'])->name('mark_all_read');
        Route::post('/{alerta}/read', [AlertaController::class, 'markAsRead'])->name('read');
        Route::get('/{alerta}', [AlertaController::class, 'show'])->name('show');
    });

    /*
    |--------------------------------------------------------------------------
    | PERFIL DE USUARIO - Acceso: ADMIN, SST, PORTERIA (cada uno su perfil)
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS ADMIN Y SST (Todo excepto gestión de usuarios)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:ADMIN,SST'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.home');

        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS
        |--------------------------------------------------------------------------
        */
        Route::prefix('vehiculos')->name('vehiculos.')->group(function () {
            Route::get('/', [VehiculoController::class, 'index'])->name('index');
            Route::get('/create', [VehiculoController::class, 'create'])->name('create');
            Route::get('/eliminados', [VehiculoController::class, 'trashed'])->name('trashed');
            Route::post('/', [VehiculoController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [VehiculoController::class, 'edit'])->name('edit');
            Route::put('/{id}', [VehiculoController::class, 'update'])->name('update');
            Route::delete('/{id}', [VehiculoController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [VehiculoController::class, 'restore'])->name('restore');

            /*
            |--------------------------------------------------------------------------
            | DOCUMENTOS DE VEHÍCULOS
            |--------------------------------------------------------------------------
            */
            Route::prefix('{vehiculo}/documentos')->name('documentos.')->group(function () {
                Route::post('/', [DocumentoVehiculoController::class, 'store'])->name('store');
                Route::get('/{documento}/edit', [DocumentoVehiculoController::class, 'edit'])->name('edit');
                Route::put('/{documento}', [DocumentoVehiculoController::class, 'update'])->name('update');
                Route::get('/historial', [DocumentoVehiculoController::class, 'historialCompleto'])->name('historial.completo');
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
            Route::get('/', [ConductorController::class, 'index'])->name('index');
            Route::get('/create', [ConductorController::class, 'create'])->name('create');
            Route::get('/eliminados', [ConductorController::class, 'trashed'])->name('trashed');
            Route::post('/', [ConductorController::class, 'store'])->name('store');
            Route::get('/{conductor}/edit', [ConductorController::class, 'edit'])->name('edit');
            Route::put('/{conductor}', [ConductorController::class, 'update'])->name('update');
            Route::delete('/{conductor}', [ConductorController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/restore', [ConductorController::class, 'restore'])->name('restore');

            // Documentos del conductor (historial y renovación)
            Route::prefix('{conductor}/documentos')->name('documentos.')->group(function () {
                Route::get('/historial', [DocumentoConductorController::class, 'historial'])->name('historial');
                Route::post('/renovar', [DocumentoConductorController::class, 'renovar'])->name('renovar');
            });
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
        | CONSULTAS Y REPORTES (Antiguo)
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
        | CENTRO DE REPORTES
        |--------------------------------------------------------------------------
        */
        Route::prefix('reportes')->name('reportes.')->group(function () {
            // Centro de reportes (dashboard)
            Route::get('/', [ReporteController::class, 'index'])->name('centro');

            // Reporte General de Vehículos
            Route::get('/vehiculos', [ReporteController::class, 'vehiculos'])->name('vehiculos');

            // Ficha detallada de un vehículo
            Route::get('/vehiculo/{id}/ficha', [ReporteController::class, 'fichaVehiculo'])->name('ficha');
            Route::get('/vehiculo/{id}/ficha/pdf', [ReporteController::class, 'fichaVehiculoPdf'])->name('ficha.pdf');

            // Reporte de Alertas y Vencimientos
            Route::get('/alertas', [ReporteController::class, 'alertas'])->name('alertas');

            // Reporte por Propietario
            Route::get('/propietarios', [ReporteController::class, 'propietarios'])->name('propietarios');

            // Reporte por Conductor
            Route::get('/conductores', [ReporteController::class, 'conductores'])->name('conductores');

            // Ficha detallada de un conductor
            Route::get('/conductor/{id}/ficha', [ReporteController::class, 'fichaConductor'])->name('ficha.conductor');
            Route::get('/conductor/{id}/ficha/pdf', [ReporteController::class, 'fichaConductorPdf'])->name('ficha.conductor.pdf');

            // Reporte Histórico
            Route::get('/historico', [ReporteController::class, 'historico'])->name('historico');

            // Exportación de reportes
            Route::get('/export/{tipo}/pdf', [ReporteController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/export/{tipo}/excel', [ReporteController::class, 'exportExcel'])->name('export.excel');
        });

        /*
        |--------------------------------------------------------------------------
        | ALERTAS - Solo ADMIN/SST (crear y eliminar)
        |--------------------------------------------------------------------------
        */
        Route::prefix('alertas')->name('alertas.')->group(function () {
            Route::post('/', [AlertaController::class, 'store'])->name('store');
            Route::delete('/{alerta}', [AlertaController::class, 'destroy'])->name('destroy');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS SOLO ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:ADMIN'])->group(function () {

        /*
        |--------------------------------------------------------------------------
        | GESTIÓN DE USUARIOS
        |--------------------------------------------------------------------------
        */
        Route::resource('usuarios', UserController::class)->except(['show']);
        Route::patch('usuarios/{usuario}/toggle-activo', [UserController::class, 'toggleActivo'])->name('usuarios.toggle-activo');

        /*
        |--------------------------------------------------------------------------
        | PERFIL - ELIMINAR (Solo admin puede eliminar cuentas)
        |--------------------------------------------------------------------------
        */
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });
});
