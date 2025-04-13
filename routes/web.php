<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CBUController;
use Illuminate\Support\Facades\Route;

// Ruta principal
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Autenticación
Route::middleware(['throttle:login'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Vista de gestión de trabajadores
    Route::get('/recursos-humanos/gestion-trabajadores', [EmpleadoController::class, 'gestionTrabajadores'])
        ->name('rrhh.gestion-trabajadores');

    // Perfil usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API RRHH
    Route::post('/empleados/guardar', [EmpleadoController::class, 'guardarEmpleado'])
        ->name('empleados.guardar');
    
    Route::get('/empleados/areas', [EmpleadoController::class, 'obtenerAreas']);
    Route::get('/empleados/cargos/{area}', [EmpleadoController::class, 'obtenerCargos']);
    Route::post('/empleados/crear-area', [EmpleadoController::class, 'crearArea']);
    Route::get('/empleados/{id}', [EmpleadoController::class, 'obtenerEmpleado']);
    Route::post('/empleados/subir-documento', [EmpleadoController::class, 'subirDocumento']);
});

// Rutas CBU
Route::post('/empleados/{empleado}/cbu', [EmpleadoController::class, 'manejarCbu'])
    ->middleware('auth')
    ->name('empleados.cbu');

Route::get('/empleados/{empleado}/cbu', [CBUController::class, 'showMasked'])
    ->name('empleados.cbu.masked');

Route::post('/empleados/{empleado}/cbu/full', [CBUController::class, 'showFull'])
    ->middleware(['auth', 'password.confirm']);
	
	Route::get('/empleados/lista', [EmpleadoController::class, 'lista'])->name('empleados.lista');


require __DIR__.'/auth.php';