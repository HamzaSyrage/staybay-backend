<?php

namespace App\Http\MiddleWares;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class VerifiedUserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = User::where('phone', $request['phone'])->first();
        if(isset($user) && is_null($user->user_verified_at))
            abort(403,'user is not verified');
        return $next($request);
    }
}
