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
            CountrySeeder::class, // Seed countries first
            // HotelSeeder::class, // Old seeder - replaced by OwnerHotelRegistrationSeeder
            // HotelImageSeeder::class, // Handled by OwnerHotelRegistrationSeeder
            // RoomImageSeeder::class, // Handled by OwnerHotelRegistrationSeeder
            HotelByCountrySeeder::class, // Seed hotels across different countries
            RoomByCountrySeeder::class, // Seed rooms for each hotel
            RoomImageByCountrySeeder::class, // Seed images for each room
            OwnerHotelRegistrationSeeder::class, // New seeder with owner registration flow
            Userseeder::class, // Keep for admin/receptionist users
            // BookingSeeder::class,
        ]);
    }
}
