<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*Ruta raíz — página principal del sitio (por defecto muestra "welcome.blade.php")
Route::get('/', function () {
    return view('welcome');
});
**/
//  Rutas públicas de autenticación (login / logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


//Ruta protegida (middleware 'auth' + 'verified')
// Solo accesible si el usuario está autenticado y su email verificado
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Grupo de rutas protegidas por autenticación

Route::middleware('auth')->group(function () {

    //Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard.home');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
