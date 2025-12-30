<?php

namespace App\Http\Middlewares;

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
            abort(403, 'You cannot pay this booking.');
        }

        if ($booking->status !== 'approved') {
            abort(422, 'Booking must be approved to pay it.');
        }

        $holdAmount = $booking->payments->sum('amount');

        if ($holdAmount >= $booking->total_price) {
            abort(422, 'Booking is already fully paid.');
        }

        if (!$booking->apartment->user) {
            abort(422, 'Apartment owner not found.');
        }

        return $next($request);
    }
}

