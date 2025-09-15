<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Amenity>
 */
class AmenityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Free Wi-Fi',
                'Swimming Pool',
                'Gym / Fitness Center',
                'Spa & Wellness',
                'Restaurant',
                'Bar / Lounge',
                'Parking',
                'Airport Shuttle',
                '24-hour Reception',
                'Room Service',
            ]),
        ];
    }
}
