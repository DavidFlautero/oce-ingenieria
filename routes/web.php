<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RRHHController;
use Illuminate\Support\Facades\Route;

// Ruta principal (muestra el login)
Route::get('/', function () {
    return view('auth.login');
})->name('home');

// Autenticación personalizada CON THROTTLE
Route::middleware(['throttle:login'])->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Recuperación de contraseña
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->middleware('guest')->name('password.request');

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Gestión de empleados (CORREGIDA - usa el controlador)
    Route::get('/recursos-humanos/gestion-trabajadores', [RRHHController::class, 'index'])
        ->name('empleados.index');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// API endpoints para RRHH
Route::middleware(['auth'])->group(function () {
    // Obtener áreas (para selects)
    Route::get('/empleados/areas', [RRHHController::class, 'obtenerAreas']);
    
    // Obtener cargos por área
    Route::get('/empleados/cargos/{area}', [RRHHController::class, 'obtenerCargos']);
    
    // Crear nueva área
    Route::post('/empleados/crear-area', [RRHHController::class, 'crearArea']);
    
    // Obtener datos de empleado
    Route::get('/empleados/{id}', [RRHHController::class, 'obtenerEmpleado']);
    
    // Guardar empleado (POST)
    Route::post('/empleados/guardar', [RRHHController::class, 'guardarEmpleado'])
        ->name('empleados.guardar');
    
    // Subir documentos
    Route::post('/empleados/subir-documento', [RRHHController::class, 'subirDocumento']);
});

// Ruta para mostrar CBU enmascarado
Route::get('/empleados/{empleado}/cbu', [CBUController::class, 'showMasked'])
     ->name('empleados.cbu.masked');

// Ruta para CBU completo (con autenticación)
Route::post('/empleados/{empleado}/cbu/full', [CBUController::class, 'showFull'])
     ->middleware(['auth', 'password.confirm']);

require __DIR__.'/auth.php';