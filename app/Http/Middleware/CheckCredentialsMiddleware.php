<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CheckCredentialsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = User::where('phone', $request['phone'])->first();
        if (!$user || !Hash::check($request['password'], $user->password)) {
            // return response()->json([
            //     'status' => 401,
            //     'message' => 'Invalid credentials',
            //     'data' => null
            // ], 401);
            abort(401, 'Invalid credentials');
        }
        return $next($request);
    }
}
