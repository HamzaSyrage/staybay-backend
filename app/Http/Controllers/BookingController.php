<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Apartment;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Who the fuck cares about this ???? //? the fucking user that want to get his bookings records
        $bookings = Booking::with(['apartment.user', 'apartment.city', 'apartment.governorate', 'apartment.images'])
            ->where('user_id', $request->user()->id)
            ->get();

        return BookingResource::collection($bookings)
            ->additional([
                'status' => 200,
                'message' => 'Bookings fetched successfully.',
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();

        $user = $request->user();

        $apartment = Apartment::findOrFail($validated['apartment_id']);

        $start = $validated['start_date'];
        $end = $validated['end_date'];

        if (!$apartment->isAvailable(Carbon::parse($start), Carbon::parse($end))) {
            abort(422, 'The apartment is not available for the selected dates.');
        }
        // start - end +1 * apartment price
        $totalPrice = (date_diff(Carbon::parse($start), Carbon::parse($end))->days + 1) * $apartment->price;


        $booking = $apartment->bookings()->create([
            'user_id' => $user->id,
            'start_date' => $start,
            'end_date' => $end,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);
        NotificationService::sendNotification(
            $apartment->user,
            'New booking needs your approval',
            [
                'booking_id' => $booking->id,
                'apartment_id' => $apartment->id,
                'type' => 'booking_approval',
            ]
        );
        $booking->refresh();

        return response()->json([
            'status' => 201,
            'message' => 'Booking created successfully.',
            'data' => BookingResource::make($booking->load(['apartment'])),
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        //user authorize
        $user = $request->user();
        $apartment = Apartment::findOrFail($booking->apartment_id);
        $validated = $request->validated();
        NotificationService::sendNotification($apartment->user,
            response()->json([
            'message'=>'booking needs approval',
            'code'=>'200',
            'user'=>$user,
            'apartment'=>$apartment,
        ]));
        $booking->update($validated);
        return response()->json([
            'message'=>'edited booking successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function cancel(Booking $booking)
    {
        $booking->update(['status'=>'cancelled']);
    }
}
