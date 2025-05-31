<?php

use App\Http\Controllers\Admin\CircularController;
use App\Http\Controllers\admin\DocumentoController;
use App\Http\Controllers\admin\MunicipioController;
use App\Http\Controllers\Admin\ProyectoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;



Route::group([
    'middleware' => 'api', // Middleware principal para la API (puede incluir throttling, etc.)
], function ($router) {
    // Rutas públicas (sin autenticación)
    Route::get('/prueba', [AuthController::class, 'prueba'])->name('prueba');
    // Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/documentos', [HomeController::class, 'allDocumentos']); // Listar los documentos de un municipio
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
// Rutas protegidas (requieren autenticación JWT)
Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    // Primero las rutas con slugs/parámetros fijos o "más específicos"
    Route::get('/municipios/search', [MunicipioController::class, 'searchUsers']); // Mueve esta arriba
    Route::post('/municipios/register', [MunicipioController::class, 'store'])->name('municipios.register');

    // Luego las rutas con parámetros de modelo comodín {municipio}
    Route::get('/municipios', [MunicipioController::class, 'index'])->name('municipios.index');
    // Route::patch('/municipios/{municipio}', [MunicipioController::class, 'update'])->name('municipios.update');
    Route::patch('municipios/{municipio}', [MunicipioController::class, 'update'])->middleware('force.form.data'); // <-- ¡Aquí aplicamos el middleware!
    Route::get('/municipios/{municipio}', [MunicipioController::class, 'show'])->name('municipios.show');
    Route::delete('/municipios/{municipio}', [MunicipioController::class, 'destroy'])->name('municipios.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'], // 'auth:api' es el middleware JWT
    'prefix' => 'admin' // Puedes usar un prefijo diferente para las rutas protegidas
], function ($router) {
    // Rutas de documentos (protegidas)
    Route::post('/municipios/{slug}/documentos', [DocumentoController::class, 'subir']); // Subir un documento
    Route::get('/municipios/{municipioId}/documentos', [DocumentoController::class, 'listar']); // Listar los documentos de un municipio
    Route::get('/documentos', [DocumentoController::class, 'listarTodos']); // Listar los documentos de un municipio
    Route::delete('/documentos/{id}', [DocumentoController::class, 'eliminar']); // Eliminar un documento
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    // Primero las rutas con slugs/parámetros fijos o "más específicos"
    // Route::get('/circulares/search', [CircularController::class, 'searchUsers']);
    Route::post('/circulares/register', [CircularController::class, 'store'])->name('circulares.register');

    // Luego las rutas con parámetros de modelo comodín {municipio}
    Route::get('/circulares', [CircularController::class, 'index'])->name('circulares.index');
    // Route::patch('/circulares/{id}', [CircularController::class, 'update'])->name('circulares.update');
    Route::get('/circulares/{id}', [CircularController::class, 'show'])->name('circulares.show');
    Route::delete('/circulares/{id}', [CircularController::class, 'eliminar'])->name('circulares.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    // Primero las rutas con slugs/parámetros fijos o "más específicos"
    // Route::get('/circulares/search', [ProyectoController::class, 'searchUsers']);
    Route::post('/proyectos/register', [ProyectoController::class, 'store'])->name('proyectos.register');

    // Luego las rutas con parámetros de modelo comodín {municipio}
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    // Route::patch('/proyectos/{id}', [ProyectoController::class, 'update'])->name('proyectos.update');
    Route::get('/proyectos/{id}', [ProyectoController::class, 'show'])->name('proyectos.show');
    Route::delete('/proyectos/{id}', [ProyectoController::class, 'eliminar'])->name('proyectos.destroy');
});
