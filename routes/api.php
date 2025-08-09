<?php

use App\Http\Controllers\admin\CategoriaController;
use App\Http\Controllers\admin\CircularController;
use App\Http\Controllers\admin\ConvenioController;
use App\Http\Controllers\admin\DocumentoController;
use App\Http\Controllers\admin\ImagenNoticiaController;
use App\Http\Controllers\admin\ImagenTuristicaController;
use App\Http\Controllers\admin\MunicipioController;
use App\Http\Controllers\admin\NoticiaController;
use App\Http\Controllers\admin\ProyectoController;
use App\Http\Controllers\admin\PublicacionController;
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
    Route::get('/municipios/{municipio}', [HomeController::class, 'getMunicipio']); // Obtener un municipio por slug
    Route::get('/documentos', [HomeController::class, 'allDocumentos']);
    Route::get('/noticias', [HomeController::class, 'getNoticias']);
    Route::get('/circulares', [HomeController::class, 'getCirculares']);
    Route::get('/convenios', [HomeController::class, 'getConvenios']);
    Route::get('/proyectos', [HomeController::class, 'getProyectos']);
    Route::get('/noticias/{slug}', [HomeController::class, 'getNoticiaForSlug']);
    Route::get('/categorias', [HomeController::class, 'getCategorias']);
    Route::get('/dashboard-totals', [HomeController::class, 'getDashboardTotals']);
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
    Route::get('/municipios/list', [MunicipioController::class, 'list'])->name('municipios.list');
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

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    // Primero las rutas con slugs/parámetros fijos o "más específicos"
    // Route::get('/circulares/search', [ProyectoController::class, 'searchUsers']);
    Route::post('/publicaciones/register', [PublicacionController::class, 'store'])->name('proyectos.register');
    // Luego las rutas con parámetros de modelo comodín {municipio}
    Route::get('/publicaciones', [PublicacionController::class, 'index'])->name('publicaciones.index');
    // Route::patch('/publicaciones/{id}', [PublicacionController::class, 'update'])->name('publicaciones.update');
    Route::get('/publicaciones/{id}', [PublicacionController::class, 'show'])->name('publicaciones.show');
    Route::delete('/publicaciones/{id}', [PublicacionController::class, 'eliminar'])->name('publicaciones.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    // Primero las rutas con slugs/parámetros fijos o "más específicos"
    // Route::get('/circulares/search', [ProyectoController::class, 'searchUsers']);
    Route::post('/convenios/register', [ConvenioController::class, 'store'])->name('convenios.register');
    // Luego las rutas con parámetros de modelo comodín {municipio}
    Route::get('/convenios', [ConvenioController::class, 'index'])->name('convenios.index');
    // Route::patch('/convenios/{id}', [ConvenioController::class, 'update'])->name('convenios.update');
    Route::get('/convenios/{id}', [ConvenioController::class, 'show'])->name('convenios.show');
    Route::delete('/convenios/{id}', [ConvenioController::class, 'eliminar'])->name('convenios.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    Route::post('/imagenes-turisticas/register', [ImagenTuristicaController::class, 'store'])->name('imagenes-turisticas.register');
    Route::get('/imagenes-turisticas', [ImagenTuristicaController::class, 'index'])->name('imagenes-turisticas.index');
    Route::get('/imagenes-turisticas/{id}', [ImagenTuristicaController::class, 'show'])->name('imagenes-turisticas.show');
    Route::delete('/imagenes-turisticas/{id}', [ImagenTuristicaController::class, 'destroy'])->name('imagenes-turisticas.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    Route::post('/categorias/register', [CategoriaController::class, 'store'])->name('categorias.register');
    Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');
    Route::get('/categorias/{categoria}', [CategoriaController::class, 'show'])->name('categorias.show');
    Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    Route::post('/noticias/register', [NoticiaController::class, 'store'])->name('noticias.register');
    Route::get('/noticias', [NoticiaController::class, 'index'])->name('noticias.index');
    Route::get('/noticias/{noticia}', [NoticiaController::class, 'show'])->name('noticias.show');
    Route::patch('/noticias/{noticia}', [NoticiaController::class, 'update'])->name('noticias.update');
    Route::delete('/noticias/{noticia}', [NoticiaController::class, 'destroy'])->name('noticias.destroy');
});

Route::group([
    'middleware' => ['api', 'auth:api'],
    'prefix' => 'admin'
], function ($router) {
    Route::post('/noticias/imagenes/register', [ImagenNoticiaController::class, 'store'])->name('noticias.imagenes.register');
    // Para listar todas las imágenes de noticias (o filtrar por noticia_id)
    Route::get('/noticias/imagenes', [ImagenNoticiaController::class, 'index'])->name('noticias.imagenes.index');
    // Para mostrar una imagen de noticia específica
    Route::get('/noticias/imagenes/{imagenNoticia}', [ImagenNoticiaController::class, 'show'])->name('noticias.imagenes.show');
    // Para actualizar una imagen de noticia específica (si se cambia el archivo o la descripción)
    // Route::patch('/noticias/imagenes/{imagenNoticia}', [ImagenNoticiaController::class, 'update'])->middleware('force.form.data')->name('noticias.imagenes.update');
    // Para eliminar una imagen de noticia específica
    Route::delete('/noticias/imagenes/{imagenNoticia}', [ImagenNoticiaController::class, 'destroy'])->name('noticias.imagenes.destroy');
});
