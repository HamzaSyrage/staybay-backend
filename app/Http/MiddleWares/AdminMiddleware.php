<?php

namespace App\Http\MiddleWares;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth('web')->check() || !auth('web')->user()->is_admin) {
            abort(403);
        }

        return $next($request);
    }

}
