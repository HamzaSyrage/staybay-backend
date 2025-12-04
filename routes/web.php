<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login',[SessionController::class,'create']);

