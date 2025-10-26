<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomByCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates rooms for each hotel with realistic data based on hotel star rating
     */
    public function run(): void
    {
        // Get all room amenities
        $roomAmenities = Amenity::where('type', 'room')->pluck('id')->toArray();

        // Get all hotels
        $hotels = Hotel::all();

        if ($hotels->isEmpty()) {
            $this->command->warn('No hotels found. Please run HotelByCountrySeeder first.');
            return;
        }

        // Room type configurations with descriptions and capacity
        $roomTypes = [
            'single' => [
                'description' => 'Kamar single dengan satu tempat tidur untuk satu orang.',
                'capacity' => 1,
                'price_multiplier' => 0.8,
            ],
            'double' => [
                'description' => 'Kamar double dengan dua tempat tidur atau satu tempat tidur besar untuk dua orang.',
                'capacity' => 2,
                'price_multiplier' => 1.0,
            ],
            'deluxe' => [
                'description' => 'Kamar deluxe yang luas dengan pemandangan kota dan fasilitas premium.',
                'capacity' => 2,
                'price_multiplier' => 1.5,
            ],
            'suite' => [
                'description' => 'Suite mewah dengan ruang tamu terpisah dan fasilitas eksklusif.',
                'capacity' => 4,
                'price_multiplier' => 2.5,
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($hotels as $hotel) {
                // Determine base price based on country and star rating
                $basePrice = $this->getBasePrice($hotel->country, $hotel->star_rating);
                
                // Number of rooms based on star rating
                $roomCount = $this->getRoomCount($hotel->star_rating);

                $roomNumber = 101; // Starting room number
                $createdRooms = 0;

                // Distribute room types based on hotel star rating
                $roomTypeDistribution = $this->getRoomTypeDistribution($hotel->star_rating, $roomCount);

                foreach ($roomTypeDistribution as $roomType => $count) {
                    for ($i = 0; $i < $count; $i++) {
                        $typeConfig = $roomTypes[$roomType];
                        
                        // Create room
                        $room = Room::create([
                            'hotel_id' => $hotel->id,
                            'room_number' => (string) $roomNumber,
                            'room_type' => $roomType,
                            'description' => $typeConfig['description'],
                            'capacity' => $typeConfig['capacity'],
                            'price_per_night' => $basePrice * $typeConfig['price_multiplier'],
                            'status' => $this->getRandomStatus(),
                        ]);

                        // Assign amenities (5-8 random amenities per room)
                        if (!empty($roomAmenities)) {
                            $amenityCount = rand(5, min(8, count($roomAmenities)));
                            $selectedAmenities = array_rand(array_flip($roomAmenities), $amenityCount);
                            $room->amenities()->sync($selectedAmenities);
                        }

                        $roomNumber++;
                        $createdRooms++;
                    }
                }

                $this->command->info("Created {$createdRooms} rooms for {$hotel->name}");
            }

            DB::commit();
            
            $totalRooms = Room::count();
            $this->command->info("\n✓ Successfully created {$totalRooms} rooms across {$hotels->count()} hotels!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get base price based on country and star rating
     */
    private function getBasePrice(string $country, int $starRating): float
    {
        // Country price multipliers (base price in USD equivalent)
        $countryPrices = [
            'Indonesia' => 50,
            'Malaysia' => 60,
            'Thailand' => 55,
            'Vietnam' => 45,
            'Philippines' => 50,
            'Singapore' => 150,
            'Japan' => 120,
            'South Korea' => 100,
            'United States' => 150,
            'United Kingdom' => 140,
            'France' => 130,
            'Australia' => 120,
            'United Arab Emirates' => 180,
        ];

        $basePrice = $countryPrices[$country] ?? 70;
        
        // Star rating multiplier
        $starMultiplier = [
            1 => 0.5,
            2 => 0.7,
            3 => 1.0,
            4 => 1.5,
            5 => 2.5,
        ];

        return $basePrice * ($starMultiplier[$starRating] ?? 1.0);
    }

    /**
     * Get number of rooms based on star rating
     */
    private function getRoomCount(int $starRating): int
    {
        return match($starRating) {
            5 => 20, // 5-star luxury hotels
            4 => 15, // 4-star hotels
            3 => 12, // 3-star hotels
            2 => 10, // 2-star hotels
            default => 8, // 1-star or budget hotels
        };
    }

    /**
     * Get room type distribution based on star rating
     */
    private function getRoomTypeDistribution(int $starRating, int $totalRooms): array
    {
        if ($starRating === 5) {
            // 5-star: More variety including suites
            return [
                'single' => (int) ($totalRooms * 0.20),  // 20%
                'double' => (int) ($totalRooms * 0.35),  // 35%
                'deluxe' => (int) ($totalRooms * 0.30),  // 30%
                'suite' => (int) ($totalRooms * 0.15),   // 15%
            ];
        } elseif ($starRating === 4) {
            // 4-star: Good variety
            return [
                'single' => (int) ($totalRooms * 0.25),  // 25%
                'double' => (int) ($totalRooms * 0.40),  // 40%
                'deluxe' => (int) ($totalRooms * 0.25),  // 25%
                'suite' => (int) ($totalRooms * 0.10),   // 10%
            ];
        } else {
            // 3-star and below: Mostly standard rooms
            return [
                'single' => (int) ($totalRooms * 0.30),  // 30%
                'double' => (int) ($totalRooms * 0.50),  // 50%
                'deluxe' => (int) ($totalRooms * 0.20),  // 20%
            ];
        }
    }

    /**
     * Get random room status with realistic distribution
     */
    private function getRandomStatus(): string
    {
        $rand = rand(1, 100);
        
        // 70% available, 20% occupied, 10% maintenance
        if ($rand <= 70) {
            return 'available';
        } elseif ($rand <= 90) {
            return 'occupied';
        } else {
            return 'maintenance';
        }
    }
}
