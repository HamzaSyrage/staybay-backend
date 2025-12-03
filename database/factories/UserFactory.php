<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $avatartsArr = [
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQD85Z0N-Y5SambnDNAFTPG7Sh-4j8RCBUbRw&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRuudCZIQEUpTylY5lYd7w7vDDxTCKFKRwCOA&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRj6x7XMbF1fRZiwlfy01w-PIp4S1CvhctnkQ&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTzAVv4TzdjQnhO-wzG3U1hdwOevrtvpMzVkQ&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvyKtJLt9jH5AkIZfyizCZxw5g3Z4ZO58IEw&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQoeTH1r3yQxqi4-DfZeG19lqm9zLawCujCmg&s',
        ];

        return [
            // 'name' => fake()->name(),
            // 'email' => fake()->unique()->safeEmail(),
            // 'email_verified_at' => now(),
            'phone' => fake()->unique()->phoneNumber(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            // 'avatar' => fake()->imageUrl('400', '400', 'people', true, 'User'),
            'avatar' => fake()->randomElement($avatartsArr),
            'birth_date' => fake()->date(),
            'id_card' => fake()->imageUrl('400', '200', 'technics', true, 'IDCard'),
            'user_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_verified_at' => null,
        ]);
    }
}
