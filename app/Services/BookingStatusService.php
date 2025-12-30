<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class BookingStatusService
{
    public static function run(): void
    {
        $today = Carbon::today();

        Booking::whereDate('start_date', $today)
            ->whereIn('status', ['pending', 'approved'])
            ->get()
            ->each(function ($booking) {
                $payments = $booking->payments;
                $netPaid = $payments->whereIn('status', ['hold', 'completed'])->sum('amount');
                $isFullyPaid = $netPaid >= $booking->total_price;
                if ($isFullyPaid) {
                    $booking->update(['status' => 'started']);
                } else {
                    $booking->update(['status' => 'failed']);
                }
            });

        Booking::whereDate('end_date', '<', $today)
            ->where('status', 'started')
            ->update(['status' => 'finished']);
    }
}
