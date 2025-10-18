<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HotelImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua hotel images yang ada
        HotelImage::query()->delete();

        // Ambil semua hotel
        $hotels = Hotel::all();

        // Array gambar yang tersedia (hotel1.jpg - hotel10.jpg)
        $availableImages = [];
        for ($i = 1; $i <= 10; $i++) {
            $availableImages[] = "hotels/hotel{$i}.jpg";
        }

        foreach ($hotels as $hotel) {
            // Random jumlah gambar antara 1-5 untuk setiap hotel
            $imageCount = rand(1, 5);

            // Shuffle array untuk mendapatkan gambar random
            $shuffledImages = $availableImages;
            shuffle($shuffledImages);

            // Ambil sejumlah imageCount gambar
            $selectedImages = array_slice($shuffledImages, 0, $imageCount);

            // Simpan ke database
            foreach ($selectedImages as $imagePath) {
                HotelImage::create([
                    'hotel_id' => $hotel->id,
                    'image_url' => $imagePath,
                ]);
            }
        }

        $this->command->info('Hotel images seeded successfully!');
    }
}
