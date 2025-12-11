<?php

use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login',[SessionController::class,'create']);
Route::post('/login',[SessionController::class,'login']);
//TODO middleWare
// Route::view('/Dashboard','User.index');
Route::get('/Dashboard',function(){
    $users = User::simplePaginate(10);
    return view('User.index', [
        'users'=>$users
    ]);
});
