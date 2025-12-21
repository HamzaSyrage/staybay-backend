<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\Booking;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::factory()->create([
            'first_name' => 'Sudo',
            'last_name' => 'Admin',
            'phone' => '1234567890',
            'password' => 'password',
            'is_admin' => true,
            'user_verified_at' => now(),
        ]);

        $governorates = Governorate::factory(5)->create();

        $cities = $governorates->flatMap(function ($governorate) {
            return City::factory(5)->create([
                'governorate_id' => $governorate->id,
            ]);
        });

        $users = User::factory(10)->create();

        Apartment::factory(20)->create([
            'user_id' => $users->random()->id,
            'governorate_id' => $governorates->random()->id,
            'city_id' => $cities->random()->id,
        ])->each(function ($apartment) use ($users) {

            ApartmentImage::factory(10)->create([
                'apartment_id' => $apartment->id,
            ]);

            $apartment->favoriteUsers()->attach(
                $users->random(3)->pluck('id')
            );

            Booking::factory(10)->create([
                'apartment_id' => $apartment->id,
                'user_id' => $users->random()->id,
            ])->each(function ($booking) {
                Payment::factory()->create([
                    'booking_id' => $booking->id,
                ]);
            });
        });
    }
}
