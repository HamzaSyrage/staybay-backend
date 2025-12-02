<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApartmentImage>
 */
class ApartmentImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apartment_id' => \App\Models\Apartment::factory(),
            'path' => $this->faker->imageUrl(640, 480, 'city', true, 'Apartment'),
        ];
    }
}
