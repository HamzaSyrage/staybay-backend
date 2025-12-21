<?php

namespace Database\Seeders;

use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Models\Booking;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            'Damascus' => [
                'Mazzeh',
                'Mazzeh Villas',
                'Rukn Al-Din',
                'Barzeh',
                'Midan',
                'Kafr Sousa',
                'Abu Rummaneh',
                'Malki',
                'Shaalan',
                'Salhieh',
                'Muhajreen',
                'Qanawat',
                'Bab Touma',
                'Bab Sharqi',
                'Jobar',
                'Qaboun',
                'Tishreen',
                'Dummar',
                'Dummar Project',
                'Mezzeh 86',
                'Kafar Souseh',
                'Zahera',
            ],
            'Rif Dimashq' => [
                'Douma',
                'Darayya',
                'Qudsaya',
                'Yabroud',
                'Al-Tall',
                'Zabadani',
                'Madaya',
                'Harasta',
                'Saqba',
                'Kafr Batna',
                'Jaramana',
                'Mleiha',
                'Ein Tarma',
                'Nabek',
                'Rankous',
            ],
            'Aleppo' => [
                'Aziziya',
                'New Aleppo',
                'Suleimaniya',
                'Hamdaneya',
                'Al-Jamiliyah',
                'Al-Sabil',
                'Al-Midan',
                'Al-Shaar',
                'Seif Al-Dawla',
                'Hanano',
                'Ramousah',
                'Karm Al-Jabal',
                'Al-Furqan',
            ],
            'Homs' => [
                'Al-Hamra',
                'Al-Zahra',
                'Al-Waer',
                'Inshaat',
                'Baba Amr',
                'Karm Al-Zeitoun',
                'Khaldiyeh',
                'Al-Arman',
            ],
            'Hama' => [
                'Al-Hader',
                'Al-Arbaeen',
                'Al-Hamidiya',
                'Bab Qibli',
                'Al-Mourabit',
                'Salamieh',
                'Masyaf',
            ],
            'Latakia' => [
                'Al-Raml Al-Janoubi',
                'Al-Raml Al-Shamali',
                'Al-Azizieh',
                'Project 10',
                'Project 7',
                'Al-Ziraa',
                'Jableh',
                'Qardaha',
            ],
            'Tartous' => [
                'Tartous City',
                'Baniyas',
                'Safita',
                'Sheikh Badr',
                'Duraykish',
            ],
            'Idlib' => [
                'Idlib City',
                'Ariha',
                'Saraqib',
                'Jisr Al-Shughur',
                'Maarrat Misrin',
            ],
            'Deir ez-Zor' => [
                'Deir ez-Zor City',
                'Al-Mayadin',
                'Al-Bukamal',
                'Ashara',
            ],
            'Raqqa' => [
                'Raqqa City',
                'Tal Abyad',
                'Al-Karamah',
            ],
            'Al-Hasakah' => [
                'Hasakah City',
                'Qamishli',
                'Ras Al-Ain',
                'Malikiyah',
            ],
            'Daraa' => [
                'Daraa City',
                'Daraa Al-Balad',
                'Izraa',
                'Al-Sanamayn',
                'Busra Al-Sham',
            ],
            'Sweida' => [
                'Sweida City',
                'Shahba',
                'Salkhad',
            ],
            'Quneitra' => [
                'Khan Arnabeh',
                'Majdal Shams',
            ],
        ];

        User::factory()->create([
            'first_name' => 'Sudo',
            'last_name' => 'Admin',
            'phone' => '1234567890',
            'password' => 'password',
            'is_admin' => true,
            'user_verified_at' => now(),
        ]);

        $governorates = collect();
        $cities = collect();

        foreach ($locations as $govName => $cityNames) {
            $governorate = Governorate::create(['name' => $govName]);
            $governorates->push($governorate);

            foreach ($cityNames as $cityName) {
                $cities->push(
                    City::create([
                        'name' => $cityName,
                        'governorate_id' => $governorate->id,
                    ])
                );
            }
        }

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
