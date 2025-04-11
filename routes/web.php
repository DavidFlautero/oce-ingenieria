<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RRHHController;
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
    Route::get('/recursos-humanos/gestion-trabajadores', [RRHHController::class, 'gestionTrabajadores'])
        ->name('rrhh.gestion-trabajadores');

    // Perfil usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // API RRHH
    Route::post('/empleados/guardar', [RRHHController::class, 'guardarEmpleado'])
        ->name('empleados.guardar');
    
    Route::get('/empleados/areas', [RRHHController::class, 'obtenerAreas']);
    Route::get('/empleados/cargos/{area}', [RRHHController::class, 'obtenerCargos']);
    Route::post('/empleados/crear-area', [RRHHController::class, 'crearArea']);
    Route::get('/empleados/{id}', [RRHHController::class, 'obtenerEmpleado']);
    Route::post('/empleados/subir-documento', [RRHHController::class, 'subirDocumento']);
});

// Rutas CBU
Route::post('/empleados/{empleado}/cbu', [RRHHController::class, 'manejarCbu'])
    ->middleware('auth')
    ->name('empleados.cbu');

Route::get('/empleados/{empleado}/cbu', [CBUController::class, 'showMasked'])
    ->name('empleados.cbu.masked');

Route::post('/empleados/{empleado}/cbu/full', [CBUController::class, 'showFull'])
    ->middleware(['auth', 'password.confirm']);

require __DIR__.'/auth.php';