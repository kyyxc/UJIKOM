<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Owner;
use App\Models\Receptionist;
use App\Models\Admin;
use App\Models\Hotel;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua hotel untuk assign owner & receptionist
        $hotelIds = Hotel::pluck('id')->toArray();

        /**
         * 1. Buat Admins
         */
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'email' => "admin{$i}@example.com",
                'password' => Hash::make('password123'),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => '0484359439',
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'profile' => null,
            ]);

            Admin::create([
                'user_id' => $user->id,
            ]);
        }

        /**
         * 2. Buat Receptionists
         */
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'email' => "r   eceptionist{$i}@example.com",
                'password' => Hash::make('password123'),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => '0484359439',
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'profile' => null,
            ]);

            Receptionist::create([
                'user_id' => $user->id,
                'hotel_id' => $i,
            ]);
        }

        /**
         * 3. Buat Owners
         */
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'email' => "owner{$i}@example.com",
                'password' => Hash::make('password123'),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => '0484359439',
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'profile' => null,
            ]);

            Owner::create([
                'user_id' => $user->id,
                'hotel_id' => $i,
            ]);
        }

        /**
         * 4. Buat Customers (user biasa tanpa relasi ke role)
         */
        for ($i = 1; $i <= 20; $i++) {
            User::create([
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password123'),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => '0484359439',
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-20 years'),
                'profile' => null,
            ]);
        }
    }
}
