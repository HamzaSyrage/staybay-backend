<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\LogoutUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::create($validated);
        // create user api token
        $token = $user->createToken
        ("api_token")->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 200,
            'message' => 'Logged in',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);
    }
    public function logout(LogoutUserRequest $request)
    {
        $validated = $request->validated();
        $request->user()->currentAccessToken()->delete();
        return response()->json(
            [
                'status' => 200,
                'message' => 'Logged in',
                'data' => null
            ]
        );
    }
}
