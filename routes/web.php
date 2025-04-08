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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/Recursos-Humanos/v/', function () {
        return view('Recursos-Humanos.v.gestionTrabajadores');
    })->name('GestionTrabajadores');

    // Perfil de usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});




// Ruta para obtener los CARGOS (puestos de trabajo) según el área seleccionada.
// Esta ruta se va a consumir desde JavaScript mediante fetch()
// El parámetro {area} es dinámico — viene del valor que el usuario selecciona en el select de Áreas.




// Ruta para obtener las Áreas dinámicamente
Route::get('/empleados/areas', [RRHHController::class, 'obtenerAreas']);

// Devuelve los Cargos según el Área seleccionada
Route::get('/empleados/cargos/{area}', [RRHHController::class, 'obtenerCargos']);

Route::post('/empleados/crear-area', [RRHHController::class, 'crearArea']);

// Ruta para obtener los datos de un empleado por su ID
Route::get('/empleados/{id}', [RRHHController::class, 'obtenerEmpleado']);

// Ruta para guardar empleados (NUEVA)
Route::post('/empleados/guardar', [RRHHController::class, 'guardarEmpleado'])->name('empleados.guardar');

// Guardar Empleado (Formulario Modal)
Route::post('/empleados/guardar', [RRHHController::class, 'guardarEmpleado'])->name('empleados.store');



require __DIR__.'/auth.php';