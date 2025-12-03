<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// use Spatie\Permission\Traits\HasRoles;
// use Spatie\Permission\Traits\HasPermissions;
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'avatar',
        'id_card',
        'birth_date',
        'password',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Booking::class);
    }

    public function favoriteApartments()
    {
        return $this->belongsToMany(Apartment::class, 'favorite_apartments');
    }
}
