<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //Who the fuck cares about this ????
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
        //
        //user authorize
        //$user = User::auth()
        $validated = $request->validate(
            [
                'user_id' => ['required', 'exists:users'],
                'apartment_id' => ['required', 'exists:apartments'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date'],
            ]
        );
        //add user to validated (remove from inline request)
        $booking = Booking::create($validated);
        return response()->json([
            'message'=>'booking done'
        ]);
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
        //
        //user authorize
        //$user = User::auth()
        $validated = $request->validate(
            [
                'user_id' => ['required', 'exists:users'],
                'apartment_id' => ['required', 'exists:apartments'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date'],
            ]
        );
        //add user to validated (remove from inline request)
        $booking->update($validated);
        return response()->json([
            'message'=>'edited booking successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        //why do i need to destroy booking ?????

    }
}
