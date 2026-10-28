<?php

use App\Http\Middleware\LoadCartFromDatabase;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            LoadCartFromDatabase::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Logout tetap harus bisa dilakukan walau token halaman sudah kedaluwarsa
        // (misalnya karena sesi lama atau halaman dibuka dari cache browser).
        // Tanpa pengecualian ini, klik Keluar bisa berakhir di halaman 419.
        $middleware->validateCsrfTokens(except: [
            'keluar',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
