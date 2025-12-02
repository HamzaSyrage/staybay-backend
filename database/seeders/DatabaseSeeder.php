<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\Booking;
use App\Models\City;
use App\Models\Country;
use App\Models\Payment;
use App\Models\Tag;
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

        $countries = Country::factory(5)->create();

        $cities = $countries->map(function ($country) {
            return City::factory(5)->create([
                'country_id' => $country->id,
            ]);
        })->flatten();

        $users = User::factory(10)->create();

        $tags = collect([
            ['name' => 'rooms'],
            ['name' => 'area'],
            ['name' => 'floor'],
            ['name' => 'wifi'],
            ['name' => 'balcony'],
            ['name' => 'pool'],
        ])->map(fn($t) => Tag::create($t));

        Apartment::factory(20)->create([
            'user_id' => $users->random()->id,
            'country_id' => $countries->random()->id,
            'city_id' => $cities->random()->id,
        ])->each(function ($apartment) use ($tags) {

            ApartmentImage::factory(10)->create([
                'apartment_id' => $apartment->id,
            ]);

            $randomTags = $tags->random(rand(2, 4));
            foreach ($randomTags as $tag) {
                $apartment->tags()->attach($tag->id, [
                    'value' => rand(1, 10),
                ]);
            }

            Booking::factory(10)->create([
                'apartment_id' => $apartment->id,
                'user_id' => User::all()->random()->id,
            ])->each(function ($booking) {

                Payment::factory()->create([
                    'booking_id' => $booking->id,
                ]);
            });
        });
    }
}
