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
            // HotelSeeder::class, // Old seeder - replaced by OwnerHotelRegistrationSeeder
            // HotelImageSeeder::class, // Handled by OwnerHotelRegistrationSeeder
            // RoomImageSeeder::class, // Handled by OwnerHotelRegistrationSeeder
            OwnerHotelRegistrationSeeder::class, // New seeder with owner registration flow
            Userseeder::class, // Keep for admin/receptionist users
            // BookingSeeder::class,
        ]);
    }
}
