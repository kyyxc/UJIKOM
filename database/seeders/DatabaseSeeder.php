<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AmenitySeeder::class,
            HotelSeeder::class,
            HotelImageSeeder::class,
            RoomImageSeeder::class,
            Userseeder::class,
            // BookingSeeder::class,
        ]);
    }
}
