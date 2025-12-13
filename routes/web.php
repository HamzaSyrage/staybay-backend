<?php
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login',[AuthController::class,'loginForm'])->name('login');
Route::post('/login',[AuthController::class,'login']);
Route::post('/logout',[AuthController::class,'logout'])->name('logout');
Route::middleware(['auth','admin'])->group(function (){
    Route::get('/dashboard',function(){
        $users = User::where('is_admin',false)->orderByRaw("CASE WHEN user_verified_at IS NULL THEN 0 ELSE 1 END, user_verified_at ASC")
            ->simplePaginate(10);
        return view('User.index', [
            'users'=>$users
        ]);
    });
    Route::post('/users/{user}/verify', [UserController::class, 'verify'])
        ->name('users.verify');
});

