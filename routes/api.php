<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\CountryController;
use Illuminate\Support\Facades\Route;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(["prefix" => "user"], function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login'])->middleware('check_user_credentials','verified_user');
    Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'apartments', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ApartmentController::class, 'index']);
    Route::post('/', [ApartmentController::class, 'store']);
    Route::get('/{apartment}', [ApartmentController::class, 'show']);

    Route::put('/{apartment}', [ApartmentController::class, 'update'])
        ->middleware('is.apartment.owner');
    Route::delete('/{apartment}', [ApartmentController::class, 'destroy'])
        ->middleware('is.apartment.owner');

});

Route::group(['prefix' => 'countries', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [CountryController::class, 'index']);

    Route::get('/{country}', [CountryController::class, 'show']);
});

// Route::group(['prefix' => 'cities', 'middleware' => 'auth:sanctum'], function () {});
