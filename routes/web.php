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
    $users = $users = User::where('is_admin',false)->orderByRaw("CASE WHEN user_verified_at IS NULL THEN 0 ELSE 1 END, user_verified_at ASC")
        ->simplePaginate(10);
    $verified_users = User::where('is_admin',false)->whereNotNull('user_verified_at');
    $unverified_users = User::where('is_admin',false)->whereNull('user_verified_at');
    $admins = User::where('is_admin',true);
    return view('User.index', [
        'users'=>$users,
        'verified_users'=>$verified_users,
        'unverified_users'=>$unverified_users,
        'admins'=>$admins
    ]);
});

