<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomImageByCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample images for each room from local storage
     */
    public function run(): void
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->command->warn('No rooms found. Please run RoomByCountrySeeder first.');
            return;
        }

        DB::beginTransaction();

        try {
            $totalImages = 0;

            foreach ($rooms as $room) {
                // Number of images based on room type (suite gets more images)
                $imageCount = $room->room_type === 'suite' ? 5 : 3;
                
                // Create random images from rooms/room1.jpg to rooms/room20.jpg
                for ($i = 0; $i < $imageCount; $i++) {
                    $randomImageNumber = rand(1, 20);
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_url' => "rooms/room{$randomImageNumber}.jpg",
                    ]);
                    $totalImages++;
                }
            }

            DB::commit();
            $this->command->info("✓ Successfully created {$totalImages} room images for {$rooms->count()} rooms!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
