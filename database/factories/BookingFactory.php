<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
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
            'user_id' => \App\Models\User::factory(),
            'start_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'end_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'cancelled', 'completed']),
            'total_price' => $this->faker->randomFloat(2, 100, 5000),
            'rating' => null,
            'rated_at' => null,
            // 'is_paid' => $this->faker->boolean(),
            // 'paid_at' => null,
        ];
    }
}
