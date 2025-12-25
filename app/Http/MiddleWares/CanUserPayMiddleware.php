<?php

namespace App\Http\Middlewares;

use App\Models\Apartment;
use Closure;
use Illuminate\Http\Request;

class CanUserPayMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $booking = $request->route('booking');

        if (!$booking) {
            abort(404, 'Booking not found.');
        }

        $user = $request->user();

        if ($booking->user_id !== $user->id) {
            abort(403, 'You cannot rate this booking.');
        }

        if ($booking->status !== 'approved') {
            abort(422, 'Booking must be approved to pay it.');
        }

        if ($booking->paid_at !== null) {
            abort(422, 'Booking has already been paid.');
        }
        return $next($request);
    }
}

