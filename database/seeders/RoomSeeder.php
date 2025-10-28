<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\Hotel;
use App\Models\Amenity;
use App\Models\RoomAmenity;
use App\Models\RoomImage;
use Faker\Factory as Faker;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        $hotels = Hotel::all();
        $roomAmenities = Amenity::where('type', 'room')->pluck('id')->toArray();

        // Room types configuration (matching enum: single, double, deluxe, suite)
        $roomTypes = [
            'single' => [
                'capacity' => 1,
                'base_price' => 300000,
            ],
            'double' => [
                'capacity' => 2,
                'base_price' => 500000,
            ],
            'deluxe' => [
                'capacity' => 2,
                'base_price' => 800000,
            ],
            'suite' => [
                'capacity' => 4,
                'base_price' => 1500000,
            ],
        ];

        $statuses = ['available', 'available', 'available', 'occupied', 'maintenance'];

        foreach ($hotels as $hotel) {
            // Each hotel gets 10-15 rooms
            $totalRooms = rand(10, 15);
            $roomNumber = 101;

            foreach ($roomTypes as $type => $config) {
                // Create 2-3 rooms per type
                $roomsOfType = rand(2, 3);
                
                for ($i = 0; $i < $roomsOfType && $roomNumber <= 100 + $totalRooms; $i++) {
                    // Adjust price based on hotel star rating
                    $priceMultiplier = match($hotel->star_rating) {
                        5 => 1.5,
                        4 => 1.2,
                        3 => 1.0,
                        default => 1.0,
                    };

                    $room = Room::create([
                        'hotel_id' => $hotel->id,
                        'room_number' => (string)$roomNumber,
                        'room_type' => $type,
                        'capacity' => $config['capacity'],
                        'price_per_night' => $config['base_price'] * $priceMultiplier,
                        'status' => $faker->randomElement($statuses),
                        'description' => $faker->sentence(10),
                    ]);

                    // Assign 4-8 random amenities
                    $selectedAmenities = $faker->randomElements($roomAmenities, rand(4, 8));
                    foreach ($selectedAmenities as $amenityId) {
                        RoomAmenity::create([
                            'room_id' => $room->id,
                            'amenity_id' => $amenityId,
                        ]);
                    }

                    // Create 2-4 room images
                    for ($j = 1; $j <= rand(2, 4); $j++) {
                        RoomImage::create([
                            'room_id' => $room->id,
                            'image_url' => "rooms/room" . rand(1, 20) . ".jpg",
                        ]);
                    }

                    $roomNumber++;
                }
            }

            $roomCount = $roomNumber - 101;
            $this->command->info("Rooms created for {$hotel->name}: {$roomCount} rooms");
        }

        $this->command->info('Rooms seeded successfully!');
    }
}
