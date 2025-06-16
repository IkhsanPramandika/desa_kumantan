<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        // ==========================================================
        // >> PERBAIKAN: Pastikan Rute API Anda Terdaftar di Sini <<
        // ==========================================================
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // --- Middleware untuk Role ---
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);

        // Blok handleCors() yang salah sudah dihapus dari sini.
        // Laravel akan menangani CORS secara otomatis untuk rute API.

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
