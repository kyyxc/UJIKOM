<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Country;
use App\Models\Owner;
use App\Models\User;
use App\Models\Amenity;
use App\Models\HotelAmenity;
use App\Models\HotelImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get all countries
        $countries = Country::all();
        $hotelAmenities = Amenity::where('type', 'hotel')->pluck('id')->toArray();

        // Hotel templates by country
        $hotelTemplates = [
            'Indonesia' => [
                'Grand Hotel Jakarta',
                'Bali Beach Resort',
                'Yogyakarta Heritage Hotel',
                'Surabaya Business Hotel',
                'Bandung Mountain Resort',
            ],
            'Malaysia' => [
                'Kuala Lumpur Tower Hotel',
                'Penang Beach Resort',
            ],
            'Singapore' => [
                'Marina Bay Luxury Hotel',
                'Sentosa Island Resort',
            ],
            'Thailand' => [
                'Bangkok Grand Palace Hotel',
                'Phuket Beach Paradise',
            ],
            'Philippines' => [
                'Manila Bay Hotel',
            ],
            'Vietnam' => [
                'Hanoi Heritage Hotel',
            ],
            'Japan' => [
                'Tokyo Imperial Hotel',
            ],
            'South Korea' => [
                'Seoul Grand Hotel',
            ],
            'China' => [
                'Beijing Forbidden City Hotel',
            ],
            'United States' => [
                'New York Manhattan Hotel',
            ],
            // European Hotels
            'France' => [
                'Paris Eiffel Tower Hotel',
                'Riviera Beach Resort Nice',
                'Lyon Historic Hotel',
            ],
            'United Kingdom' => [
                'London Tower Bridge Hotel',
                'Edinburgh Castle Hotel',
                'Manchester City Centre Hotel',
            ],
            'Germany' => [
                'Berlin Brandenburg Hotel',
                'Munich Oktoberfest Hotel',
            ],
            'Italy' => [
                'Rome Colosseum Hotel',
                'Venice Canal Grande Hotel',
                'Florence Renaissance Hotel',
            ],
            'Spain' => [
                'Barcelona Sagrada Familia Hotel',
                'Madrid Royal Palace Hotel',
            ],
            'Netherlands' => [
                'Amsterdam Canal Hotel',
            ],
            'Switzerland' => [
                'Zurich Alpine Hotel',
            ],
            'Austria' => [
                'Vienna Opera House Hotel',
            ],
        ];

        $starRatings = [3, 4, 5];

        foreach ($countries as $country) {
            $countryName = $country->name;
            
            if (!isset($hotelTemplates[$countryName])) {
                continue;
            }

            foreach ($hotelTemplates[$countryName] as $hotelName) {
                // Determine city based on hotel name or country
                $city = $this->getCityFromHotelName($hotelName, $countryName);
                
                // Create hotel
                $hotel = Hotel::create([
                    'name' => $hotelName,
                    'description' => $faker->paragraph(3),
                    'address' => $faker->streetAddress,
                    'city' => $city,
                    'state_province' => $faker->state,
                    'country' => $countryName,
                    'latitude' => $faker->latitude(),
                    'longitude' => $faker->longitude(),
                    'email' => strtolower(str_replace(' ', '', $hotelName)) . '@hotel.com',
                    'website' => 'https://' . strtolower(str_replace(' ', '', $hotelName)) . '.com',
                    'star_rating' => $faker->randomElement($starRatings),
                    'check_in_time' => '14:00:00',
                    'check_out_time' => '12:00:00',
                    'cancellation_policy' => $faker->paragraph(2),
                    'is_active' => true,
                ]);

                // Create owner for this hotel
                $hotelSlug = strtolower(str_replace(' ', '', $hotelName));
                $ownerUser = User::create([
                    'email' => "owner+{$hotelSlug}@gmail.com",
                    'password' => Hash::make('password123'),
                    'first_name' => $faker->firstName,
                    'last_name' => $faker->lastName,
                    'phone' => $faker->phoneNumber,
                    'address' => $faker->address,
                    'date_of_birth' => $faker->dateTimeBetween('-55 years', '-30 years'),
                    'profile' => null,
                ]);

                // Create owner record and assign to hotel with approved status
                Owner::create([
                    'user_id' => $ownerUser->id,
                    'hotel_id' => $hotel->id,
                    'bank_name' => $faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga']),
                    'account_number' => $faker->numerify('##########'),
                    'account_holder_name' => $ownerUser->first_name . ' ' . $ownerUser->last_name,
                    'business_license_number' => $faker->numerify('SIUP-####-####-####'),
                    'business_license_file' => 'documents/siup_' . $hotel->id . '.pdf',
                    'tax_id_number' => $faker->numerify('##.###.###.#-###.###'),
                    'tax_id_file' => 'documents/npwp_' . $hotel->id . '.pdf',
                    'identity_card_file' => 'documents/ktp_' . $hotel->id . '.pdf',
                    'registration_status' => 'approved',
                    'submitted_at' => now()->subDays(rand(10, 30)),
                    'approved_at' => now()->subDays(rand(1, 9)),
                ]);

                // Assign random amenities (5-8 amenities per hotel)
                $selectedAmenities = $faker->randomElements($hotelAmenities, rand(5, 8));
                foreach ($selectedAmenities as $amenityId) {
                    HotelAmenity::create([
                        'hotel_id' => $hotel->id,
                        'amenity_id' => $amenityId,
                    ]);
                }

                // Create 3-5 hotel images (placeholder paths)
                for ($i = 1; $i <= rand(3, 5); $i++) {
                    HotelImage::create([
                        'hotel_id' => $hotel->id,
                        'image_url' => "hotels/hotel" . rand(1, 20) . ".jpg",
                    ]);
                }

                $this->command->info("Hotel created: {$hotelName} in {$countryName}");
            }
        }

        $this->command->info('Hotels seeded successfully!');
    }

    /**
     * Extract city from hotel name
     */
    private function getCityFromHotelName(string $hotelName, string $countryName): string
    {
        // Extract city from hotel name
        $cities = [
            // Asia
            'Jakarta' => 'Jakarta',
            'Bali' => 'Denpasar',
            'Yogyakarta' => 'Yogyakarta',
            'Surabaya' => 'Surabaya',
            'Bandung' => 'Bandung',
            'Kuala Lumpur' => 'Kuala Lumpur',
            'Penang' => 'George Town',
            'Marina Bay' => 'Singapore',
            'Sentosa' => 'Singapore',
            'Bangkok' => 'Bangkok',
            'Phuket' => 'Phuket',
            'Manila' => 'Manila',
            'Hanoi' => 'Hanoi',
            'Tokyo' => 'Tokyo',
            'Seoul' => 'Seoul',
            'Beijing' => 'Beijing',
            // North America
            'New York' => 'New York',
            // Europe
            'Paris' => 'Paris',
            'Riviera' => 'Nice',
            'Nice' => 'Nice',
            'Lyon' => 'Lyon',
            'London' => 'London',
            'Edinburgh' => 'Edinburgh',
            'Manchester' => 'Manchester',
            'Berlin' => 'Berlin',
            'Munich' => 'Munich',
            'Rome' => 'Rome',
            'Venice' => 'Venice',
            'Florence' => 'Florence',
            'Barcelona' => 'Barcelona',
            'Madrid' => 'Madrid',
            'Amsterdam' => 'Amsterdam',
            'Zurich' => 'Zurich',
            'Vienna' => 'Vienna',
        ];

        foreach ($cities as $keyword => $city) {
            if (stripos($hotelName, $keyword) !== false) {
                return $city;
            }
        }

        // Default fallback
        return explode(' ', $countryName)[0];
    }
}
