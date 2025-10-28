<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            // Hotel Amenities
            ['name' => 'WiFi', 'type' => 'hotel'],
            ['name' => 'Swimming Pool', 'type' => 'hotel'],
            ['name' => 'Gym', 'type' => 'hotel'],
            ['name' => 'Restaurant', 'type' => 'hotel'],
            ['name' => 'Spa', 'type' => 'hotel'],
            ['name' => 'Bar', 'type' => 'hotel'],
            ['name' => 'Parking', 'type' => 'hotel'],
            ['name' => 'Airport Shuttle', 'type' => 'hotel'],
            ['name' => 'Conference Room', 'type' => 'hotel'],
            ['name' => '24/7 Reception', 'type' => 'hotel'],
            
            // Room Amenities
            ['name' => 'Air Conditioning', 'type' => 'room'],
            ['name' => 'TV', 'type' => 'room'],
            ['name' => 'Mini Bar', 'type' => 'room'],
            ['name' => 'Safe Box', 'type' => 'room'],
            ['name' => 'Coffee Maker', 'type' => 'room'],
            ['name' => 'Hair Dryer', 'type' => 'room'],
            ['name' => 'Bathtub', 'type' => 'room'],
            ['name' => 'Work Desk', 'type' => 'room'],
            ['name' => 'Balcony', 'type' => 'room'],
            ['name' => 'Room Service', 'type' => 'room'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }

        $this->command->info('Amenities seeded successfully!');
    }
}
