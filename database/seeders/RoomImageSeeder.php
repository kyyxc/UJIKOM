<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua room images yang ada
        RoomImage::query()->delete();

        // Ambil semua room
        $rooms = Room::all();

        // Array gambar yang tersedia (room1.jpg - room20.jpg)
        $availableImages = [];
        for ($i = 1; $i <= 20; $i++) {
            $availableImages[] = "rooms/room{$i}.jpg";
        }

        foreach ($rooms as $room) {
            // Random jumlah gambar antara 1-5 untuk setiap room
            $imageCount = rand(1, 5);

            // Shuffle array untuk mendapatkan gambar random
            $shuffledImages = $availableImages;
            shuffle($shuffledImages);

            // Ambil sejumlah imageCount gambar
            $selectedImages = array_slice($shuffledImages, 0, $imageCount);

            // Simpan ke database
            foreach ($selectedImages as $imagePath) {
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_url' => $imagePath,
                ]);
            }
        }

        $this->command->info('Room images seeded successfully!');
    }
}
