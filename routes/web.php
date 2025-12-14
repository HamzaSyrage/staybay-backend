<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
Route::permanentRedirect('/','/dashboard');
Route::get('/login',[AuthController::class,'loginForm'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->name('logout');
Route::middleware(['auth','admin'])->group(function (){
    Route::get('/dashboard',[DashboardController::class,'index'])->name('dashboard');
    Route::prefix('/users/{user}/')->group(function (){
        Route::get('show',[UserController::Class , 'show'])->name('users.show');
        Route::post('verify', [UserController::class, 'verify'])
            ->name('users.verify');
        Route::Delete('delete', [UserController::class , 'destroy'])->name('users.destroy');
    });

});

