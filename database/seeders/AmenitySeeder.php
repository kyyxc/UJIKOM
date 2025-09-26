<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Amenities khusus untuk Hotel
        $hotelAmenities = [
            'Kolam Renang',
            'Spa & Sauna',
            'Gym',
            'Restoran',
            'Parkir Gratis',
            'Bar & Lounge',
            'Ruang Meeting',
            'Laundry',
            'Layanan Antar Jemput',
            'Resepsionis 24 Jam',
        ];

        foreach ($hotelAmenities as $name) {
            Amenity::firstOrCreate([
                'name' => $name,
                'type' => 'hotel',
            ]);
        }

        // Amenities khusus untuk Room
        $roomAmenities = [
            'WiFi Gratis',
            'AC',
            'TV Kabel',
            'Kulkas Mini',
            'Brankas',
            'Meja Kerja',
            'Shower Air Panas',
            'Pembuat Kopi/Teh',
            'Setrika',
            'Peralatan Mandi Gratis',
        ];

        foreach ($roomAmenities as $name) {
            Amenity::firstOrCreate([
                'name' => $name,
                'type' => 'room',
            ]);
        }
    }
}
