<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;



Route::group([
    'middleware' => 'api', // Middleware principal para la API
    'prefix' => 'auth'
], function ($router) {
    // Rutas públicas (sin autenticación)
    Route::get('/prueba', [AuthController::class, 'prueba'])->name('prueba');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    // Rutas que requieren autenticación (token JWT)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
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



