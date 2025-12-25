<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'apartment_id',
        'start_date',
        'end_date',
        'total_price',
        'status',
        'rating',
        'rated_at',
        'paid_at',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
    public function CalculatePrice(){
        return (date_diff(Carbon::parse($this->start_date), Carbon::parse($this->end_date))->days + 1) * $this->apartment->price;
    }
}
