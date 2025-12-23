<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\MiddleWares\AdminMiddleware;
use App\Http\MiddleWares\VerifiedUserMiddleware;
use App\Http\MiddleWares\CheckCredentialsMiddleware;
use App\Http\MiddleWares\IsApartmentOwnerMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'verified_user' => VerifiedUserMiddleware::class,
            'check_user_credentials' => CheckCredentialsMiddleware::class,
            'is_apartment_owner' => IsApartmentOwnerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
