<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsApartmentOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apartment = $request->route('apartment');

        if ($apartment && $apartment->user_id !== $request->user()->id) {
            return response()->json([
                'status' => 403,
                'message' => 'You are not authorized to update this apartment.',
            ], 403);
        }

        return $next($request);
    }
}
