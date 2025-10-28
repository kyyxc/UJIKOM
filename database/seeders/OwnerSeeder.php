<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Owner;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Note: Owner sekarang dibuat langsung di HotelSeeder dengan format email owner+namahotel@gmail.com
     * Seeder ini tidak digunakan lagi
     */
    public function run(): void
    {
        // Owner creation moved to HotelSeeder for better hotel-owner association
        $this->command->info('Owner users will be created in HotelSeeder with format: owner+hotelname@gmail.com');
    }
}
