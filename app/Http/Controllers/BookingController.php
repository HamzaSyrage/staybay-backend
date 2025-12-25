<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayBookingRequest;
use App\Http\Requests\RatingBookingRequest;
use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Http\Requests\UpdateOwnerBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Apartment;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //Who the fuck cares about this ???? //? maybe the fucking user that want to get his bookings records
        $bookings = Booking::with(['apartment.user', 'apartment.city', 'apartment.governorate', 'apartment.images'])
            ->where('user_id', $request->user()->id)
            ->get();

        return BookingResource::collection($bookings)
            ->additional([
                'status' => 200,
                'message' => 'ur Bookings fetched successfully.',
            ])
            ->response()
            ->setStatusCode(200);
    }
    public function own(Request $request)
    {
        $ownerId = $request->user()->id;

        $bookings = Booking::with([
            'apartment.user',
            'apartment.city',
            'apartment.governorate',
            'apartment.images'
        ])
            ->whereHas('apartment', function ($query) use ($ownerId) {
                $query->where('user_id', $ownerId);
            })
            ->get();

        return BookingResource::collection($bookings)
            ->additional([
                'status' => 200,
                'message' => 'Bookings for your apartments.',
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
        $validated = $request->validated();
        $user = $request->user();
        $apartment = $booking->apartment;

        if (($validated['status'] ?? null) === 'cancelled') {
            $booking->update([
                'status' => 'cancelled',
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Booking cancelled successfully.',
                'data' => BookingResource::make($booking),
            ]);
        }

        if (isset($validated['start_date'], $validated['end_date'])) {

            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);

            if (
                !$apartment->isAvailable($start, $end, $booking->id ?? null)
            ) {
                abort(422, 'The apartment is not available for the selected dates.');
            }

            $totalPrice = ($start->diffInDays($end) + 1) * $apartment->price;

            $booking->update([
                'start_date' => $start,
                'end_date' => $end,
                'total_price' => $totalPrice,
                'status' => 'pending',
            ]);

            NotificationService::sendNotification(
                $apartment->user,
                response()->json([
                    'message' => 'booking needs approval',
                    'code' => '200',
                    'user' => $user,
                    'apartment' => $apartment,
                ])
            );
        }

        return response()->json([
            'status' => 200,
            'message' => 'Booking updated successfully.',
            'data' => BookingResource::make($booking->load('apartment')),
        ]);
    }

    public function owner_update(
        UpdateOwnerBookingRequest $request,
        Booking $booking
    ) {
        if ($booking->status === 'cancelled') {
            abort(422, 'Cancelled bookings cannot be approved or rejected.');
        }
        $validated = $request->validated();

        $booking->update([
            'status' => $validated['status'],
        ]);

        NotificationService::sendNotification(
            $booking->user,
            'Your booking was ' . $validated['status'],
            [
                'booking_id' => $booking->id,
                'apartment_id' => $booking->apartment_id,
                'status' => $validated['status'],
                'type' => 'booking_status_update',
            ]
        );

        return response()->json([
            'status' => 200,
            'message' => 'Booking status updated successfully.',
            'data' => BookingResource::make(
                $booking->load(['apartment'])
            ),
        ]);
    }


    public function pay(Booking $booking, PayBookingRequest $request)
    {
        $user = $request->user();

        if ($booking->user_id !== $user->id) {
            abort(403, 'You are not allowed to pay for this booking.');
        }

        if ($booking->status !== 'approved') {
            abort(422, 'Booking must be approved before payment.');
        }

        if ($booking->paid_at) {
            abort(422, 'Booking already paid.');
        }

        $owner = $booking->apartment->user;
        if (!$owner) {
            abort(422, 'Apartment owner not found.');
        }

        DB::transaction(function () use ($user, $owner, $booking) {
            $user->transferBalanceTo($owner, $booking->total_price);

            $booking->paid_at = now();
            $booking->status = 'completed';
            $booking->save();
        });

        $booking->refresh();

        NotificationService::sendNotification(
            $user,
            'Payment successful',
            [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'type' => 'payment_success',
            ]
        );

        NotificationService::sendNotification(
            $owner,
            'You received a payment',
            [
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'type' => 'payment_received',
            ]
        );

        return response()->json([
            'status' => 200,
            'message' => 'Payment completed successfully.',
            'data' => BookingResource::make($booking->load(['apartment'])),
        ]);
    }

    public function rate(Booking $booking, RatingBookingRequest $request)
    {
        $user = $request->user();

        if ($booking->user_id !== $user->id) {
            abort(403, 'You are not allowed to rate this booking.');
        }

        if ($booking->rated_at !== null) {
            abort(422, 'Booking already rated.');
        }

        if ($booking->status !== 'completed') {
            abort(422, 'Booking must be completed before rating.');
        }

        $validated = $request->validated();
        $booking->update([
            'rating' => $validated['rating'],
            'rated_at' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Booking rated successfully.',
            'data' => BookingResource::make($booking),
        ]);
    }
}
