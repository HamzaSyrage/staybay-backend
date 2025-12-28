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
use Illuminate\Validation\ValidationException;
class BookingController extends Controller
{
    // public function apartmentNotAvailableIn($id)
    // {
    //     return Booking::where('apartment_id', $id)
    //         ->whereIn('status', ['approved', 'completed'])
    //         ->where('start_date', '>=', Carbon::now())
    //         ->get(['start_date', 'end_date']);
    // }

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
                $query->select('prev_id')->from('bookings')->whereNotNull('prev_id');
            })
            ->get();

        return BookingResource::collection($bookings)
            ->additional(['status' => 200, 'message' => 'Your bookings fetched successfully.'])
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
            ->whereHas('apartment', fn($query) => $query->where('user_id', $ownerId))
            ->whereNotIn('id', function ($query) {
                $query->select('prev_id')->from('bookings')->whereNotNull('prev_id');
            })
            ->get();

        return BookingResource::collection($bookings)
            ->additional(['status' => 200, 'message' => 'Bookings for your apartments.'])
            ->response()
            ->setStatusCode(200);
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        $apartment = Apartment::findOrFail($validated['apartment_id']);

        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();

        // if ($end->lt($start)) {
        //     throw ValidationException::withMessages([
        //         'dates' => 'End date must be after start date.',
        //     ]);
        // }

        $booking = DB::transaction(function () use ($apartment, $user, $start, $end) {

            if (!$apartment->isAvailable($start, $end)) {
                throw ValidationException::withMessages([
                    'dates' => 'The apartment is not available for the selected dates.',
                ]);
            }

            $totalPrice = ($start->diffInDays($end) + 1) * $apartment->price;

            return $apartment->bookings()->create([
            'user_id' => $user->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
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

        return BookingResource::make($booking->load(['apartment', 'payments']))
            ->additional([
                'status' => 201,
                'message' => 'Booking created successfully.',
            ])
            ->response()
            ->setStatusCode(201);
    }

    // I DID REMOVE THE PREV BOOKING THING TOTTALY
    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        $validated = $request->validated();
        $user = $request->user();
        $apartment = $booking->apartment;

        // CANCEL BOOKING
        if (isset($validated['status']) && $validated['status'] === 'cancelled') {

            DB::transaction(function () use ($booking) {
                $user = $booking->user;
                $owner = $booking->apartment->user;

                $totalPaid = $booking->payments()->where('status', 'hold')->sum('amount');

                if ($totalPaid > 0) {
                    $penalty = round($totalPaid * (Booking::$penalty / 100), 2);
                    $refund = $totalPaid - $penalty;

                    $booking->payments()->where('status', 'hold')->update([
                        'status' => 'refunded',
                        'payment_date' => now(),
                    ]);

                    $user->hold_balance -= $totalPaid;
                    $owner->hold_balance -= $totalPaid;

                    $user->balance += $refund;
                    $owner->balance += $penalty;

                    $user->save();
                    $owner->save();
                }

                $booking->update([
                    'status' => 'cancelled',
                ]);
            });

            NotificationService::sendNotification(
                $booking->user,
                'Booking cancelled',
                ['booking_id' => $booking->id, 'penalty' => Booking::$penalty . '%', 'type' => 'booking_cancelled']
            );

            return BookingResource::make($booking->fresh()->load(['apartment', 'payments']))
                ->additional(['status' => 200, 'message' => 'Booking cancelled with penalty applied.'])
                ->response();
        }

        // EDIT DATES
        if (isset($validated['start_date'], $validated['end_date'])) {
            $start = Carbon::parse($validated['start_date'])->startOfDay();
            $end = Carbon::parse($validated['end_date'])->startOfDay();

            if (!$apartment->isAvailable($start, $end, $booking->id)) {
                throw ValidationException::withMessages([
                    'dates' => 'The apartment is not available for the selected dates.',
                ]);
            }

            $oldTotalPaid = $booking->payments()->whereIn('status', ['hold', 'completed'])->sum('amount');
            $newTotalPrice = ($start->diffInDays($end) + 1) * $apartment->price;

            DB::transaction(function () use ($booking, $start, $end, $newTotalPrice, $oldTotalPaid, $user) {

                $booking->update([
                    'status' => 'pending',
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                ]);

                if ($oldTotalPaid > $newTotalPrice) {
                    $priceDiff = $oldTotalPaid - $newTotalPrice;
                    $penaltyAmount = round($priceDiff * (Booking::$penalty / 100), 2);
                    $refund = $priceDiff - $penaltyAmount;

                    $booking->payments()->whereIn('status', ['hold', 'completed'])->update([
                        'status' => 'refunded',
                        'payment_date' => now(),
                    ]);

                    $user->balance += $refund;
                    $user->hold_balance -= $oldTotalPaid;
                    $user->save();

                    NotificationService::sendNotification(
                        $user,
                        "Booking updated: a refund of $refund was issued after a penalty of $penaltyAmount",
                        ['booking_id' => $booking->id, 'type' => 'booking_refund']
                    );

                    $booking->update(['total_price' => $newTotalPrice]);

                } elseif ($oldTotalPaid < $newTotalPrice) {
                    $remainingToPay = $newTotalPrice - $oldTotalPaid;
                    $booking->update(['total_price' => $remainingToPay]);

                    NotificationService::sendNotification(
                        $user,
                        "Booking updated: you need to pay the remaining amount: " . $remainingToPay,
                        ['booking_id' => $booking->id, 'type' => 'booking_pending_payment']
                    );
                }
            });


            return BookingResource::make($booking->fresh()->load(['apartment', 'payments']))
                ->additional(['status' => 200, 'message' => 'Booking dates updated successfully.'])
                ->response();
        }

        return response()->json([
            'status' => 422,
            'message' => 'Nothing to update.',
        ], 422);
    }



    public function owner_update(UpdateOwnerBookingRequest $request, Booking $booking)
    {
        $validated = $request->validated();
        $owner = $request->user();

        DB::transaction(function () use ($booking, $validated) {

            $user = $booking->user;

            // APPROVED
            if ($validated['status'] === 'approved') {
                $booking->update(['status' => 'approved']);
                // Nothing TO DO USER CAN PAY NOW
            }

            // REJECTED
            if ($validated['status'] === 'rejected') {
                $totalPaid = $booking->payments()->whereIn('status', ['hold', 'completed'])->sum('amount');

                if ($totalPaid > 0) { // GIVE THE USER HIS MONEY BACK
                    $booking->payments()->update([
                        'status' => 'refunded',
                        'payment_date' => now(),
                    ]);
                    $user->balance += $totalPaid;
                    $user->hold_balance -= $totalPaid;
                    $user->save();
            }

                $booking->update(['status' => 'rejected']);
        }
        });

        NotificationService::sendNotification(
            $booking->user,
            "Your booking was " . $validated['status'],
            ['booking_id' => $booking->id, 'apartment_id' => $booking->apartment_id, 'type' => 'booking_status_update']
        );

        return BookingResource::make($booking->fresh()->load(['apartment', 'payments']))
            ->additional(['status' => 200, 'message' => 'Booking status updated successfully.'])
            ->response();
    }


    public function pay(Booking $booking, PayBookingRequest $request)
    {
        //USER CAN PAY ONLY AFTER APPROVED
        $user = $request->user();
        $owner = $booking->apartment->user;

        $paidAmount = $booking->payments()->whereIn('status', ['hold', 'completed'])->sum('amount');
        $remaining = max(0, $booking->total_price - $paidAmount);

        if ($remaining <= 0) {
            abort(422, 'Booking is already fully paid.');
        }

        $amountToPay = min($user->balance, $remaining);

        if ($amountToPay <= 0) {
            abort(422, 'You do not have enough balance to pay the remaining amount.');
        }

        DB::transaction(function () use ($user, $owner, $booking, $amountToPay) {
            $booking->payments()->create([
                'amount' => $amountToPay,
                'status' => 'hold',
                'payment_date' => now(),
            ]);

            $user->transferOnHoldTo($owner, $amountToPay);
        });

        NotificationService::sendNotification(
            $user,
            'Payment successful',
            ['booking_id' => $booking->id, 'amount' => $amountToPay, 'type' => 'payment_success']
        );

        NotificationService::sendNotification(
            $owner,
            'You received a payment',
            ['booking_id' => $booking->id, 'amount' => $amountToPay, 'type' => 'payment_received']
        );

        $booking->update(['status' => 'completed']);
        $booking->refresh();

        return BookingResource::make($booking->load(['apartment', 'payments']))
            ->additional([
                'status' => 200,
                'message' => $amountToPay < $remaining
                    ? "Partial payment processed. Remaining amount: " . ($remaining - $amountToPay)
                    : "Payment placed on hold successfully. It will be released to the owner when the booking starts.",
            ])
            ->response()
            ->setStatusCode(200);
    }

    public function rate(Booking $booking, RatingBookingRequest $request)
    {
        $validated = $request->validated();

        DB::transaction(function () use ($booking, $validated) {
            $booking->update([
                'rating' => $validated['rating'],
                'rated_at' => Carbon::now(),
            ]);
            $booking->apartment->reCalculateRating();
        });

        return BookingResource::make($booking->load(['apartment']))
            ->additional(['status' => 200, 'message' => 'Booking rated successfully.'])
            ->response()
            ->setStatusCode(200);
    }
}
