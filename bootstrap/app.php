<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function ($router) {
            Route::prefix('api')
                ->middleware('api')
                ->name('api.')
                ->group(base_path('routes/role-permission.php'));
                Route::prefix('api')
                ->middleware('api')
                ->name('api.')
                ->group(base_path('routes/auth.php'));
        }



    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            \G4T\Swagger\Middleware\SetJsonResponseMiddleware::class,
        ]);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleCheckMiddleware::class,
            'RPManagement' => \App\Http\Middleware\PermissionCheckMiddleware::class,
        ]);


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
