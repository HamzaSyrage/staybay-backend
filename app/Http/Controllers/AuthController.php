<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\LogoutUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
         $validated = $request->validated();
         $user = User::create($validated);
//         create user api token
//         $token = $user->createToken("api_token")->plainTextToken;
        return response()->json([
            'user' => $user,
//            'token' => $token
        ]);
    }
    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();


    }
    public function logout(LogoutUserRequest $request)
    {
//        $validated = $request->validated();
//        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
