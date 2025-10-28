<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create 5 Admin users
        for ($i = 1; $i <= 5; $i++) {
            $user = User::create([
                'email' => "admin{$i}@hotelmanagement.com",
                'password' => Hash::make('password123'),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-50 years', '-25 years'),
                'profile' => null,
            ]);

            Admin::create([
                'user_id' => $user->id,
            ]);

            $this->command->info("Admin {$i} created: {$user->email}");
        }

        $this->command->info('Admin users seeded successfully!');
    }
}
