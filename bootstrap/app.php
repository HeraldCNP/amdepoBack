<?php

use App\Http\Middleware\EnsureRoleExists;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            // Aquí es donde añadimos nuestro middleware
            // \App\Http\Middleware\ForceFormDataParse::class,
        ]);
        // $middleware->alias([
        //     'role.exists' => EnsureRoleExists::class, // Registra el alias del middleware
        // ]);

        // Define tus aliases de middleware aquí:
        $middleware->alias([
            // 'auth' => \App\Http\Middleware\Authenticate::class, // Ejemplo de alias de auth
            'force.form.data' => \App\Http\Middleware\ForceFormDataParse::class, // <-- ¡Añade esta línea!
        ]);


        // Middleware globales (ejecutados en cada petición)
        // $middleware->prepend(EnsureRoleExists::class); // Ejemplo si fuera global

        // Middleware para grupos (ej. para la API - si lo necesitas)
        // $middleware->for('api', function (Middleware $middleware) {
        //     $middleware->push(EnsureRoleExists::class);
        // });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 401);
            }
        });
    })->create();
