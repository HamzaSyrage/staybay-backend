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
        return $this->images()->where('is_cover', true)->first();
    }
    public function rating_count()
    {
        return $this->bookings()
            ->whereNotNull('rating')
            ->count();
    }

    public function isAvailable(Carbon $start, Carbon $end, ?int $ignoreBookingId = null): bool
    {
        $start = $start->toDateString();
        $end = $end->toDateString();

        return !$this->bookings()
            ->when($ignoreBookingId, fn($q) => $q->where('id', '!=', $ignoreBookingId))
            ->whereIn('status', ['approved', 'started', 'completed', 'pending'])
            // ->where('is_latest', true)
            ->where(function ($q) use ($start, $end) {
                $q->where('start_date', '<=', $end)
                    ->where('end_date', '>=', $start);
            })
            ->exists();
    }




    public function notAvailableDates(): array
    {
        $dates = $this->bookings()
            ->whereIn('status', ['approved', 'completed', 'pending', 'started'])
            // ->where('is_latest', true)
            ->where('end_date', '>=', Carbon::today())
            ->get(['start_date', 'end_date'])
            ->map(fn($b) => [
                'start_date' => Carbon::parse($b->start_date),
                'end_date' => Carbon::parse($b->end_date),
            ])
            ->sortBy('start_date')
            ->values()
            ->toArray();

        if (empty($dates)) {
            return [];
        }

        $merged = [];
        $current = $dates[0];

        foreach ($dates as $i => $date) {
            if ($i === 0)
                continue;


            if ($date['start_date']->lte($current['end_date']->copy()->addDay())) {

                $current['end_date'] = $date['end_date']->max($current['end_date']);
            } else {

                $merged[] = [
                    'start_date' => $current['start_date']->toDateString(),
                    'end_date' => $current['end_date']->toDateString(),
                ];
                $current = $date;
            }
        }

        $merged[] = [
            'start_date' => $current['start_date']->toDateString(),
            'end_date' => $current['end_date']->toDateString(),
        ];

        return $merged;
    }


    public function reCalculateRating()
    {
        $query = $this->bookings()->whereNotNull('rating');
        $avgRating = $query->avg('rating') ?? 0;
        $this->rating = round($avgRating, 1);
        // $this->rating_count = $query->count(); ? we dont have a rating count col we calc it in response only
        $this->save();
    }
}
