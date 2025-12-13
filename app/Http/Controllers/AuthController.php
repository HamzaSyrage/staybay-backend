<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginDashboardRequest;
use App\Http\Requests\LogoutUserRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginDashboardRequest $request)
    {
        //$credentials = $request->validated();

        if(Auth::attempt($request->credentials())){
            $request->session()->regenerate();
            return redirect('/dashboard');
        }
        return redirect()->back()->withErrors(['this errorrrrr']);
    }
    public function loginForm()
    {
        return view('login');
    }
    public function logout(LogoutUserRequest $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
