<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\LogoutUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    public function register(RegisterUserRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');

            $fullUrl = asset('storage/' . $path);
            $validated['avatar'] = $fullUrl;
        }

        if ($request->hasFile('id_card')) {
            $path = $request->file('id_card')->store('id_cards', 'public');
            $fullUrl = asset('storage/' . $path);
            $validated['id_card'] = $fullUrl;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $token = $user->createToken('api_token')->plainTextToken;

        return UserResource::make($user)->additional([
            'status' => 201,
            'message' => 'User registered successfully',
            'data' => [
            'token' => $token
            ]
        ])->response()->setStatusCode(201);
    }

    public function login(LoginUserRequest $request)
    {
        $validated = $request->validated();

        $user = User::where('phone', $validated['phone'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return (new UserResource($user))->additional([
            'status' => 200,
            'message' => 'Login ok',
            'data' => [
                'token' => $token
            ]
        ]);
    }
    public function logout(LogoutUserRequest $request)
    {
        if (!$request->user()) {
            return UserResource::make(null)->additional([
                'status' => 401,
                'message' => 'Unauthenticated',
                'data' => null,
            ])->response()->setStatusCode(401);
        }
        $request->user()->currentAccessToken()->delete();
        return UserResource::make(null)->additional([
            'status' => 200,
            'message' => 'Logged out',
            'data' => null
        ])->response()->setStatusCode(200);
    }
}
