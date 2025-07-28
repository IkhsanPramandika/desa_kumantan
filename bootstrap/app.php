<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\KepalaDesaMiddleware;
use App\Http\Middleware\RoleMiddleware; // Asumsi ini adalah middleware 'role' yang sudah ada

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // [PERBAIKAN] Menambahkan middleware Sanctum ke grup 'api'
        // Ini akan membuat Laravel mengenali user dari token API
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Middleware alias Anda yang sudah ada dan penambahan 'kepala_desa'
        $middleware->alias([
            'role' => RoleMiddleware::class, // Middleware 'role' Anda yang sudah ada
            'kepala_desa' => KepalaDesaMiddleware::class, // Tambahkan baris ini
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
    
