
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UsuarioController;

// Rutas públicas
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas protegidas con autenticación y rol válido
Route::middleware(['auth',])->group(function () {
    Route::get('/', [AuthController::class, 'dashboard'])->name('dashboard');
    //panel principal sst y admin
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.home');
    //gestion de usuarios solo admin

    Route::middleware('is.Admin')->group(function () {
        Route::resource('usuarios', UsuarioController::class)->except(['show']);
    });

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
