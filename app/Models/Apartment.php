<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    /** @use HasFactory<\Database\Factories\ApartmentFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        // 'governorate_id',
        'city_id',
        'title',
        'description',
        'price',
        'rating',
        'bathrooms',
        'bedrooms',
        'size',
        'has_pool',
        'has_wifi',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function governorate()
    // {
    //     return $this->city->governorate();
    // }
    public function governorate()
    {
        return $this->hasOneThrough(
            Governorate::class,
            City::class,
            'id',
            'id',
            'city_id',
            'governorate_id'
        );
    }
    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function images()
    {
        return $this->hasMany(ApartmentImage::class);
    }
    // public function tags()
    // {
    //     return $this->belongsToMany(Tag::class, 'apartment_tags');
    // }

    public function favoriteUsers()
    {
        return $this->belongsToMany(User::class, 'favorite_apartments');
    }
    public function cover_image()
    {
        return $this->images()->first();
    }
    public function rating_count()
    {
        return $this->bookings()
            ->whereNotNull('rating')
            ->count();
    }

    public function isAvailable(
        Carbon $start,
        Carbon $end,
        ?int $BookingId = null
    ): bool {
        return !$this->bookings()
            ->when($BookingId, function ($q) use ($BookingId) {
                $q->where('id', '!=', $BookingId);
            })
            ->where(function ($query) use ($start, $end) {
                $query
                    ->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();
    }
}
