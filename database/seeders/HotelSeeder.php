<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Amenity;
use App\Models\HotelImage;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\RoomImage;
use App\Models\HotelAmenity;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil amenities berdasarkan type
        $hotelAmenities = Amenity::where('type', 'hotel')->pluck('id')->toArray();
        $roomAmenities  = Amenity::where('type', 'room')->pluck('id')->toArray();

        // Buat 5 hotel dulu (bisa diubah jadi 20)
        for ($i = 1; $i <= 20; $i++) {
            $hotel = Hotel::create([
                'name' => "Grand Luxury Hotel {$i}",
                'description' => "Hotel mewah nomor {$i} dengan fasilitas modern.",
                'address' => "Jl. Sudirman No. {$i}, Jakarta",
                'city' => 'Jakarta',
                'state_province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'latitude' => -6.200000 + ($i * 0.001),
                'longitude' => 106.816666 + ($i * 0.001),
                'email' => "info{$i}@grandluxuryhotel.com",
                'website' => "https://grandluxuryhotel{$i}.com",
                'star_rating' => rand(3, 5),
                'cancellation_policy' => 'Gratis pembatalan hingga 24 jam sebelum check-in.',
                'is_active' => true,
            ]);

            // Tambahkan hotel image
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => 'hotel1.jpg',
            ]);

            // Hubungkan hotel dengan amenities hotel
            foreach ($hotelAmenities as $amenityId) {
                HotelAmenity::create([
                    'hotel_id' => $hotel->id,
                    'amenity_id' => $amenityId,
                ]);
            }

            // Buat 4 rooms
            $rooms = [
                ['room_number' => "10{$i}", 'room_type' => 'single', 'capacity' => 1, 'price_per_night' => 500000],
                ['room_number' => "20{$i}", 'room_type' => 'double', 'capacity' => 2, 'price_per_night' => 750000],
                ['room_number' => "30{$i}", 'room_type' => 'deluxe', 'capacity' => 3, 'price_per_night' => 1200000],
                ['room_number' => "40{$i}", 'room_type' => 'suite', 'capacity' => 4, 'price_per_night' => 2500000],
            ];

            foreach ($rooms as $roomData) {
                $room = Room::create([
                    'hotel_id' => $hotel->id,
                    'room_number' => $roomData['room_number'],
                    'room_type' => $roomData['room_type'],
                    'description' => "Kamar tipe {$roomData['room_type']} nyaman di Hotel {$i}",
                    'capacity' => $roomData['capacity'],
                    'price_per_night' => $roomData['price_per_night'],
                    'status' => 'available',
                ]);

                // Tambahkan room image
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_url' => 'room1.jpg',
                ]);

                // Hubungkan room dengan amenities room
                foreach ($roomAmenities as $amenityId) {
                    RoomAmenity::create([
                        'room_id' => $room->id,
                        'amenity_id' => $amenityId,
                    ]);
                }
            }
        }
    }
}
