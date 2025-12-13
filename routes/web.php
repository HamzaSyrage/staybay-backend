<?php
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\AuthController;
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login',[AuthController::class,'loginForm'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->name('logout');

Route::get('/dashboard',function(){
    $users = User::where('is_admin',false)->orderByRaw("CASE WHEN user_verified_at IS NULL THEN 0 ELSE 1 END, user_verified_at ASC")
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
})->middleware('auth');

