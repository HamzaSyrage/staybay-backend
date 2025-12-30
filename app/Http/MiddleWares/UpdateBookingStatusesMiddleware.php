<?php

namespace App\Http\MiddleWares;

use App\Services\BookingStatusService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UpdateBookingStatusesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Cache::add('booking-status-run', true, 60)) {
            BookingStatusService::run();
        }
        return $next($request);
    }
}
