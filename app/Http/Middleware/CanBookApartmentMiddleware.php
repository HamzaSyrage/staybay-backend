<?php

namespace App\Http\Middleware;

use App\Models\Apartment;
use Closure;
use Illuminate\Http\Request;

class CanBookApartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        $apartmentId = $request->input('apartment_id');

        if (!$apartmentId) {
             abort(422, 'Apartment is required.');
        }

        $apartment = Apartment::find($apartmentId);

        if (!$apartment) {
             abort(404, 'Apartment not found.');
        }

        if ($apartment->user_id === $user->id) {
            abort(403, 'You cannot book your own apartment.');
        }

        return $next($request);
    }
}
