<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Create 30 Customer users (regular users without specific role)
        for ($i = 1; $i <= 30; $i++) {
            $user = User::create([
                'email' => "customer{$i}@example.com",
                'password' => Hash::make('password123'),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'phone' => $faker->phoneNumber,
                'address' => $faker->address,
                'date_of_birth' => $faker->dateTimeBetween('-60 years', '-18 years'),
                'profile' => null,
            ]);

            if ($i % 10 == 0) {
                $this->command->info("Customer {$i} created: {$user->email}");
            }
        }

        $this->command->info('Customer users seeded successfully!');
    }
}
