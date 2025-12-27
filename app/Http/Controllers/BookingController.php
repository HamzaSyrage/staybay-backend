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
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function apartmentNotAvailableIn($id)
    {
        return Booking::where('apartment_id', $id)
            ->whereIn('status', ['approved', 'completed'])
            ->where('start_date', '>=', Carbon::now())
            ->get(['start_date', 'end_date']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bookings = Booking::with([
            'apartment.user',
            'apartment.city',
            'apartment.governorate',
            'apartment.images',
            'payments'
        ])
            ->where('user_id', $request->user()->id)
            ->whereNotIn('id', function ($query) {
                $query->select('prev_id')
                    ->from('bookings')
                    ->whereNotNull('prev_id');
            })
            ->get();

        return BookingResource::collection($bookings)
            ->additional([
                'status' => 200,
                'message' => 'Your bookings fetched successfully.',
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
            'apartment.images',
            'payments'
        ])
            ->whereHas('apartment', function ($query) use ($ownerId) {
                $query->where('user_id', $ownerId);
            })
            ->whereNotIn('id', function ($query) {
                $query->select('prev_id')
                    ->from('bookings')
                    ->whereNotNull('prev_id');
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
     * Store a newly created resource in storage.
     */
    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        $apartment = Apartment::findOrFail($validated['apartment_id']);
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);

        $booking = DB::transaction(function () use ($apartment, $user, $start, $end) {

            $existing = $apartment->bookings()
                ->whereNotIn('status', ['rejected', 'cancelled'])
                ->where(function ($query) use ($start, $end) {
                    $query->where('start_date', '<=', $end)
                        ->where('end_date', '>=', $start);
                })
                ->lockForUpdate()
                ->exists();

            if ($existing) {
            abort(422, 'The apartment is not available for the selected dates.');
            }

            $totalPrice = ($start->diffInDays($end) + 1) * $apartment->price;

            return $apartment->bookings()->create([
            'user_id' => $user->id,
            'start_date' => $start,
            'end_date' => $end,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);
        });

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

        return BookingResource::make($booking->load(['apartment', 'payments']))
            ->additional([
                'status' => 201,
                'message' => 'Booking created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
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
        $new_booking = null;

        if (isset($validated['status']) && $validated['status'] === 'cancelled') {
            $booking->update(['status' => $validated['status']]);

            return BookingResource::make($booking->load(['apartment']))
                ->additional([
                    'status' => 200,
                    'message' => 'Booking cancelled successfully.',
                ])
                ->response()
                ->setStatusCode(200);
        }


        if ($booking->status === 'approved' && isset($validated['start_date'], $validated['end_date'])) {
            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);

            if (!$apartment->isAvailable($start, $end, $booking->id)) {
                abort(422, 'The apartment is not available for the selected dates.');
            }

            $totalPrice = ($start->diffInDays($end) + 1) * $apartment->price;

            $new_booking = $apartment->bookings()->create([
                'user_id' => $user->id,
                'start_date' => $start,
                'end_date' => $end,
                'total_price' => $totalPrice,
                'prev_id' => $booking->id,
            ]);

            NotificationService::sendNotification(
                $apartment->user,
                response()->json([
                    'message' => 'Booking needs approval',
                    'user' => $user,
                    'apartment' => $apartment,
                    'old_booking' => $booking,
                    'new_booking' => $new_booking,
                ])
            );
        } else {

            $booking->update($validated);
        }

        return response()->json([
            'status' => 200,
            'message' => 'Booking updated successfully.',
            'data' => [
                'booking' => BookingResource::make($booking->load(['apartment', 'payments'])),
                'new_booking' => $new_booking ? BookingResource::make($new_booking->load(['apartment', 'payments'])) : null,
            ],
        ]);
    }


    public function owner_update(UpdateOwnerBookingRequest $request, Booking $booking)
    {
        $editedBooking = Booking::where('prev_id', $booking->id)->first();
        $validated = $request->validated();

        DB::transaction(function () use ($booking, $editedBooking, $validated) {

            $booking->update(['status' => $validated['status']]);

            if (!$editedBooking) {
                return;
            }

            $user = $editedBooking->user;
            $owner = $editedBooking->apartment->user;

            if ($validated['status'] === 'approved') {
                $refunded = 0;
                foreach ($booking->payments as $payment) {
                    $refunded += $payment->amount;
                    $payment->update([
                        'status' => 'refunded',
                        'payment_date' => now(),
                    ]);
                }

                $user->hold_balance -= $refunded;
                $owner->hold_balance -= $refunded;
                $user->save();
                $owner->save();

                $price = $editedBooking->total_price;

                $amountToHold = min($refunded, $price);
                if ($amountToHold > 0) {
                    $editedBooking->payments()->create([
                        'amount' => $amountToHold,
                        'status' => 'hold',
                        'payment_date' => now(),
                    ]);
                    $user->transferOnHoldTo($owner, $amountToHold);
            }

                if ($refunded < $price) {
                    $editedBooking->update([
                        'total_price' => $price - $refunded,
                ]);
                } elseif ($refunded > $price) {
                    $overflow = $refunded - $price;
                    $user->balance += $overflow;
                    $user->save();
            }

                $editedBooking->prev_id = null;
                $editedBooking->save();

                $booking->delete();

            } elseif ($validated['status'] === 'rejected') {
                $editedBooking->delete();
                $booking->update(['status' => 'pending']);
        }
        });

        NotificationService::sendNotification(
            $editedBooking->user ?? $booking->user,
            'Your booking was ' . $validated['status'],
            [
                'booking_id' => $editedBooking->id ?? $booking->id,
                'apartment_id' => $editedBooking->apartment_id ?? $booking->apartment_id,
                'type' => 'booking_status_update',
            ]
        );

        $currentBooking = $editedBooking ?? $booking;
        $currentBooking->refresh();

        return BookingResource::make(
            $currentBooking->load(['apartment', 'payments'])
        )->additional([
                    'status' => 200,
                    'message' => 'Booking updated successfully.',
                ])->response();
    }

    public function pay(Booking $booking, PayBookingRequest $request)
    {
        $user = $request->user();
        $owner = $booking->apartment->user;

        if (!($user->balance >= $booking->total_price)) {
            abort(422, 'You do not have enough balance for this transfer.');
        }

        DB::transaction(function () use ($user, $owner, $booking) {

            $payments = $booking->payments()->create([
                'amount' => $booking->total_price,
                'status' => 'hold',
                'payment_date' => now(),
            ]);

            $user->transferOnHoldTo($owner, $booking->total_price);
        });

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
        $booking->refresh();
        return BookingResource::make($booking->load(['apartment', 'payments']))
            ->additional([
                'status' => 200,
                'message' => 'Payment placed on hold successfully. It will be released to the owner when the booking starts.',
            ])
            ->response()
            ->setStatusCode(200);
    }


    public function rate(Booking $booking, RatingBookingRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();
        DB::transaction(function () use ($booking, $validated) {
            $booking->update([
                'rating' => $validated['rating'],
                'rated_at' => Carbon::now(),
            ]);
            $booking->apartment->reCalculateRating();
        });
        $booking->refresh();

        return BookingResource::make($booking->load(['apartment']))
            ->additional([
                'status' => 200,
                'message' => 'Booking rated successfully.',
            ])
            ->response()
            ->setStatusCode(200);
    }
}
