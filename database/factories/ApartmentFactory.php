<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            // 'governorate_id' => \App\Models\Governorate::factory(),
            'city_id' => \App\Models\City::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->numberBetween(50, 500),
            'rating' => 0,
            'bathrooms' => $this->faker->numberBetween(1, 10),
            'bedrooms' => $this->faker->numberBetween(1, 5),
            'size' => $this->faker->numberBetween(20, 200),
            'has_pool' => $this->faker->boolean(),
            'has_wifi' => $this->faker->boolean(),
        ];
    }
}
