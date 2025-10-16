<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::prefix('api/owner')
                ->middleware(['api', 'auth:sanctum'])
                ->group(base_path('routes/api_owner.php'));
            
            Route::prefix('api/admin')
                ->middleware(['api', 'auth:sanctum'])
                ->group(base_path('routes/api_admin.php'));
            
            Route::prefix('api/receptionist')
                ->middleware(['api', 'auth:sanctum'])
                ->group(base_path('routes/api_receptionist.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('api', \Illuminate\Http\Middleware\HandleCors::class);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'owner' => \App\Http\Middleware\OwnerMiddleware::class,
            'receptionist' => \App\Http\Middleware\ReceptionistMiddleware::class,
            'customer' => \App\Http\Middleware\CustomerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
