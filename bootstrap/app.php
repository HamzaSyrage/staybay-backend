<?php

use App\Http\Middleware\UpdateBookingStatusesMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\CanBookApartmentMiddleware;
use App\Http\Middleware\CanUserPayMiddleware;
use App\Http\Middleware\CanUserRateMiddleware;
use App\Http\Middleware\VerifiedUserMiddleware;
use App\Http\Middleware\CheckCredentialsMiddleware;
use App\Http\Middleware\IsApartmentOwnerMiddleware;

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
            'can_book_apartment' => CanBookApartmentMiddleware::class,
            'can_user_rate' => CanUserRateMiddleware::class,
            'can_user_pay' => CanUserPayMiddleware::class,

            'update_booking_statuses' => UpdateBookingStatusesMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
