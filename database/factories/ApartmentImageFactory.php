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
        $imagesArr = [
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTNeGvj38WW2Kf2yN70oIm0Kw39pV3GsTewfg&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkxKiUMuRPNEy3V8X0FlIcQjs4e6lnbbjqQA&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTFFq4TUCRaDl9NWoVmh-a6I2BFB5eGBbBO1Q&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRBvzbRpNxrolVoSKhmhN09i1sagHZ3lNsMKw&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcToO5omFVocgfsEipInluWv6V7EsQNDUsHv4g&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTZI7tx4aXr7cjTeuDNKutqjs2izdUpdZzPMw&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpCNmOuZvp3_B2n3faRAlltS6wIv1MWZkstQ&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTSUOY_oYlZUNjweROrXkL6wle6fiGmLm0XzQ&s',
            'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFfB30YxjXbuIA4oOG64AHYrUwfXOl4k7zwA&s'
        ];
        return [
            'apartment_id' => \App\Models\Apartment::factory(),
            'path' => $this->faker->randomElement($imagesArr),
        ];
    }
}
