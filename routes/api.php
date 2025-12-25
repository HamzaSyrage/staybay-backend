<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GovernorateController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group(["prefix" => "user"], function () {
    Route::post('/register', [ApiAuthController::class, 'register']);
    Route::post('/login', [ApiAuthController::class, 'login'])->middleware('check_user_credentials','verified_user');
    Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/notifications', [NotificationController::class, 'myNotifications'])->middleware('auth:sanctum');;
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->middleware('auth:sanctum');
    Route::post('/notifications', [NotificationController::class, 'markAllAsRead'])->middleware('auth:sanctum');
});

Route::group(['prefix' => 'apartments', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ApartmentController::class, 'index']);
    Route::post('/', [ApartmentController::class, 'store']);
    Route::get('/my', [ApartmentController::class, 'my']);

    Route::get('/favorite', [ApartmentController::class, 'favorite']);
    Route::post('/favorite/add/{apartment}', [ApartmentController::class, 'add_favorite']);
    Route::delete('/favorite/remove/{apartment}', [ApartmentController::class, 'remove_favorite']);


    Route::get('/{apartment}', [ApartmentController::class, 'show']);
    Route::get('/nonAvailableDates', [BookingController::class, 'apartmentNotAvailableIn']);
    Route::put('/{apartment}', [ApartmentController::class, 'update'])
        ->middleware('is_apartment_owner');
    Route::delete('/{apartment}', [ApartmentController::class, 'destroy'])
        ->middleware('is_apartment_owner');


});
Route::group(['prefix' => 'chat' , 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [ChatController::class, 'index']);
    Route::post('/send', [ChatController::class, 'send']);
    Route::get('/{chat}', [ChatController::class, 'show']);
    Route::delete('/{chat}', [ChatController::class, 'destroy']);
});
Route::group(['prefix' => 'message' , 'middleware' => 'auth:sanctum'], function () {
    Route::patch('/{message}', [MessageController::class, 'edit']);
    Route::delete('/{message}', [MessageController::class, 'destroy']);
    Route::patch('/{message}/read', [MessageController::class, 'read']);
});
Route::group(['prefix' => 'governorates', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [GovernorateController::class, 'index']);

    Route::get('/{governorate}', [GovernorateController::class, 'show']);
});

// Route::group(['prefix' => 'cities', 'middleware' => 'auth:sanctum'], function () {});
Route::group(['prefix' => 'bookings', 'middleware' => 'auth:sanctum'], function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store'])->middleware('can_book_apartment');
    Route::get('/own', [BookingController::class, 'own']);
    Route::put('/update/user/{booking}', [BookingController::class, 'update']);
    Route::put('/update/owner/{booking}', [BookingController::class, 'owner_update']);
    Route::post('/pay/{booking}', [BookingController::class, 'pay'])->middleware('can_user_pay');
    Route::post('/rate/{booking}', [BookingController::class, 'rate'])->middleware('can_user_rate');
});
