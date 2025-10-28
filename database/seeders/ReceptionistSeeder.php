<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Receptionist;
use App\Models\Hotel;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class ReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Setiap hotel akan memiliki 1-2 receptionist
     */
    public function run(): void
    {
        $faker = Faker::create();
        $hotels = Hotel::all();

        foreach ($hotels as $hotel) {
            // Create 1-2 receptionists per hotel
            $numReceptionists = rand(1, 2);
            
            $hotelSlug = strtolower(str_replace(' ', '', $hotel->name));
            
            for ($i = 1; $i <= $numReceptionists; $i++) {
                $emailSuffix = $numReceptionists > 1 ? "{$i}" : "";
                $user = User::create([
                    'email' => "receptionist{$emailSuffix}+{$hotelSlug}@gmail.com",
                    'password' => Hash::make('password123'),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'date_of_birth' => $faker->dateTimeBetween('-45 years', '-22 years'),
                    'profile' => null,
                ]);

                Receptionist::create([
                    'user_id' => $user->id,
                    'hotel_id' => $hotel->id,
                ]);

                $this->command->info("Receptionist created for {$hotel->name}: {$user->email}");
            }
        }

        $this->command->info('Receptionist users seeded successfully!');
    }
}
