<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    /** @use HasFactory<\Database\Factories\ApartmentFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'country_id',
        'city_id',
        'title',
        'description',
        'price',
        'rating',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
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
}
