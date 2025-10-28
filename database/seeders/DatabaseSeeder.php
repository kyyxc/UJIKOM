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
            // 1. Seed Master Data
            CountrySeeder::class,
            AmenitySeeder::class,
            
            // 2. Seed Users by Role
            AdminSeeder::class,
            CustomerSeeder::class,
            OwnerSeeder::class,
            
            // 3. Seed Hotels (will assign owners to hotels)
            HotelSeeder::class,
            
            // 4. Seed Receptionists (after hotels are created)
            ReceptionistSeeder::class,
            
            // 5. Seed Rooms (after hotels are created)
            RoomSeeder::class,
            
            // 6. Seed Bookings (optional - can be commented out)
            BookingSeeder::class,
        ]);
    }
}
