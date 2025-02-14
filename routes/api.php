<?php

use App\Http\Controllers\admin\MunicipioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;



Route::group([
    'middleware' => 'api', // Middleware principal para la API (puede incluir throttling, etc.)
    'prefix' => 'auth'
], function ($router) {
    // Rutas públicas (sin autenticación)
    Route::get('/prueba', [AuthController::class, 'prueba'])->name('prueba');
    // Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Rutas protegidas (requieren autenticación JWT)
Route::group([
    'middleware' => ['api', 'auth:api'], // 'auth:api' es el middleware JWT
    'prefix' => 'auth' // Puedes usar un prefijo diferente para las rutas protegidas
], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Rutas CRUD de usuarios (protegidas)
    Route::get('/users', [AuthController::class, 'index'])->name('users.index');
    Route::patch('/users/{id}', [AuthController::class, 'update'])->name('users.update'); // O PATCH
    Route::post('/users/register', [AuthController::class, 'register'])->name('users.register');
    Route::get('/users/{id}', [AuthController::class, 'show'])->name('users.show');
    Route::delete('/users/{id}', [AuthController::class, 'destroy'])->name('users.destroy');
    Route::get('/user/search', [AuthController::class, 'searchUsers']);
});

Route::group([
    'middleware' => ['api', 'auth:api'], // Middleware para la API y autenticación
    // 'middleware' => ['api', 'auth:api', 'can:manage roles'],
    'prefix' => 'admin' // Prefijo para rutas de administración (opcional)
], function ($router) {
    // Rutas protegidas que requieren autenticación para la gestión de roles y permisos
    Route::controller(RoleController::class)->prefix('roles')->group(function () {
        Route::get('/{role}', 'show');
        Route::post('/', 'store');
        Route::put('/{role}', 'update');
        Route::delete('/{role}', 'destroy');
        Route::get('/', 'index');
    });
    Route::get('/permissions', [RoleController::class, 'getAllPermissions']);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('roles.assignPermissions');
});

// Rutas protegidas (requieren autenticación JWT)
Route::group([
    'middleware' => ['api', 'auth:api'], // 'auth:api' es el middleware JWT
    'prefix' => 'admin' // Puedes usar un prefijo diferente para las rutas protegidas
], function ($router) {
    // Rutas CRUD de usuarios (protegidas)
    Route::get('/municipios', [MunicipioController::class, 'index'])->name('municipios.index');
    Route::patch('/municipios/{id}', [MunicipioController::class, 'update'])->name('municipios.update'); // O PATCH
    Route::post('/municipios/register', [MunicipioController::class, 'store'])->name('municipios.register');
    Route::get('/municipios/{slug}', [MunicipioController::class, 'show'])->name('municipios.show');
    Route::delete('/municipios/{id}', [MunicipioController::class, 'destroy'])->name('municipios.destroy');
    Route::get('/municipios/search', [MunicipioController::class, 'searchUsers']);
});
