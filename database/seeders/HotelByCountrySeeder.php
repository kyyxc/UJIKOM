<?php

namespace Database\Seeders;

use App\Models\Amenity;
use App\Models\Hotel;
use App\Models\HotelImage;
use App\Models\Owner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HotelByCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates hotels across different countries with realistic data
     */
    public function run(): void
    {
        // Get all hotel amenities
        $hotelAmenities = Amenity::where('type', 'hotel')->pluck('id')->toArray();

        // Get all owners for assignment (if exists)
        $owners = Owner::all();
        $ownerIndex = 0;

        $hotels = [
            // INDONESIA
            [
                'name' => 'Grand Hyatt Jakarta',
                'description' => 'Hotel bintang 5 mewah di pusat kota Jakarta dengan pemandangan skyline yang menakjubkan. Dilengkapi dengan fasilitas kelas dunia dan layanan premium.',
                'address' => 'Jl. M.H. Thamrin No.28-30',
                'city' => 'Jakarta',
                'state_province' => 'DKI Jakarta',
                'country' => 'Indonesia',
                'latitude' => -6.195068,
                'longitude' => 106.822975,
                'phone' => '+62-21-29921234',
                'email' => 'info@grandhyattjakarta.com',
                'website' => 'https://grandhyattjakarta.com',
                'star_rating' => 5,
                'check_in_time' => '14:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Pembatalan gratis hingga 48 jam sebelum check-in. Pembatalan setelah itu dikenakan biaya 1 malam.',
                'is_active' => true,
            ],
            [
                'name' => 'The Trans Resort Bali',
                'description' => 'Resort mewah di tepi pantai Seminyak dengan private beach, spa kelas dunia, dan infinity pool menghadap Samudra Hindia.',
                'address' => 'Jl. Sunset Road No.105',
                'city' => 'Seminyak',
                'state_province' => 'Bali',
                'country' => 'Indonesia',
                'latitude' => -8.692556,
                'longitude' => 115.166420,
                'phone' => '+62-361-7390000',
                'email' => 'reservation@transresortbali.com',
                'website' => 'https://transresortbali.com',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Pembatalan gratis 72 jam sebelum check-in. Setelah itu dikenakan 100% dari total biaya.',
                'is_active' => true,
            ],
            [
                'name' => 'Plataran Borobudur Resort',
                'description' => 'Resort heritage eksklusif di kaki Candi Borobudur dengan arsitektur Jawa klasik dan keindahan alam yang memukau.',
                'address' => 'Dusun Tanjungan, Borobudur',
                'city' => 'Magelang',
                'state_province' => 'Jawa Tengah',
                'country' => 'Indonesia',
                'latitude' => -7.605556,
                'longitude' => 110.203889,
                'phone' => '+62-293-788888',
                'email' => 'info@plataranborobudur.com',
                'website' => 'https://plataranborobudur.com',
                'star_rating' => 5,
                'check_in_time' => '14:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Pembatalan gratis 7 hari sebelum check-in.',
                'is_active' => true,
            ],

            // MALAYSIA
            [
                'name' => 'Petronas Tower Hotel',
                'description' => 'Hotel megah di jantung Kuala Lumpur dengan pemandangan langsung ke Petronas Twin Towers. Kemewahan dan kenyamanan dalam satu tempat.',
                'address' => 'Jalan Ampang, KLCC',
                'city' => 'Kuala Lumpur',
                'state_province' => 'Wilayah Persekutuan',
                'country' => 'Malaysia',
                'latitude' => 3.157786,
                'longitude' => 101.711746,
                'phone' => '+60-3-21234567',
                'email' => 'info@petronastowerhotel.com',
                'website' => 'https://petronastowerhotel.com',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in.',
                'is_active' => true,
            ],
            [
                'name' => 'Langkawi Beach Resort',
                'description' => 'Tropical paradise resort di pulau Langkawi dengan private beach, water sports center, dan sunset bar yang romantis.',
                'address' => 'Pantai Cenang',
                'city' => 'Langkawi',
                'state_province' => 'Kedah',
                'country' => 'Malaysia',
                'latitude' => 6.295306,
                'longitude' => 99.725306,
                'phone' => '+60-4-9551234',
                'email' => 'reservation@langkawibeach.com',
                'website' => 'https://langkawibeach.com',
                'star_rating' => 4,
                'check_in_time' => '14:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation 72 hours before arrival.',
                'is_active' => true,
            ],

            // SINGAPORE
            [
                'name' => 'Marina Bay Luxury Hotel',
                'description' => 'Iconic luxury hotel with rooftop infinity pool overlooking Marina Bay. World-class dining and entertainment at your doorstep.',
                'address' => '10 Bayfront Avenue',
                'city' => 'Singapore',
                'state_province' => 'Central Region',
                'country' => 'Singapore',
                'latitude' => 1.283611,
                'longitude' => 103.860833,
                'phone' => '+65-6688-8888',
                'email' => 'info@marinabayluxury.com',
                'website' => 'https://marinabayluxury.com',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Free cancellation 24 hours before check-in. Late cancellation charged 1 night.',
                'is_active' => true,
            ],
            [
                'name' => 'Sentosa Island Resort',
                'description' => 'Premium beach resort on Sentosa Island with direct beach access, multiple pools, and kids club facilities.',
                'address' => '2 Bukit Manis Road, Sentosa',
                'city' => 'Singapore',
                'state_province' => 'Southern Islands',
                'country' => 'Singapore',
                'latitude' => 1.250556,
                'longitude' => 103.827778,
                'phone' => '+65-6275-0331',
                'email' => 'reservation@sentosaresort.sg',
                'website' => 'https://sentosaresort.sg',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Full refund if cancelled 3 days before arrival.',
                'is_active' => true,
            ],

            // THAILAND
            [
                'name' => 'Bangkok Grand Palace Hotel',
                'description' => 'Luxury hotel near Grand Palace with traditional Thai architecture and modern amenities. Perfect blend of culture and comfort.',
                'address' => '123 Maharaj Road, Phra Nakhon',
                'city' => 'Bangkok',
                'state_province' => 'Bangkok',
                'country' => 'Thailand',
                'latitude' => 13.750000,
                'longitude' => 100.491667,
                'phone' => '+66-2-123-4567',
                'email' => 'info@bangkokgrandpalace.com',
                'website' => 'https://bangkokgrandpalace.com',
                'star_rating' => 5,
                'check_in_time' => '14:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation 48 hours prior to arrival.',
                'is_active' => true,
            ],
            [
                'name' => 'Phuket Paradise Resort',
                'description' => 'Beachfront resort in Patong Beach with stunning ocean views, multiple dining options, and traditional Thai spa.',
                'address' => '456 Thaweewong Road, Patong',
                'city' => 'Phuket',
                'state_province' => 'Phuket',
                'country' => 'Thailand',
                'latitude' => 7.893611,
                'longitude' => 98.296667,
                'phone' => '+66-76-340-106',
                'email' => 'reservation@phuketparadise.com',
                'website' => 'https://phuketparadise.com',
                'star_rating' => 4,
                'check_in_time' => '14:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Cancellation free of charge until 7 days before arrival.',
                'is_active' => true,
            ],

            // VIETNAM
            [
                'name' => 'Hanoi Heritage Hotel',
                'description' => 'Boutique hotel in Old Quarter with French colonial architecture, rooftop restaurant, and traditional Vietnamese hospitality.',
                'address' => '88 Hang Trong Street, Hoan Kiem',
                'city' => 'Hanoi',
                'state_province' => 'Hanoi',
                'country' => 'Vietnam',
                'latitude' => 21.028889,
                'longitude' => 105.852778,
                'phone' => '+84-24-3826-8888',
                'email' => 'info@hanoiheritage.vn',
                'website' => 'https://hanoiheritage.vn',
                'star_rating' => 4,
                'check_in_time' => '14:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation 3 days before check-in.',
                'is_active' => true,
            ],
            [
                'name' => 'Da Nang Beach Resort',
                'description' => 'Modern beachfront resort with private beach, infinity pool, and stunning views of Marble Mountains.',
                'address' => 'Vo Nguyen Giap Street, Non Nuoc',
                'city' => 'Da Nang',
                'state_province' => 'Da Nang',
                'country' => 'Vietnam',
                'latitude' => 16.067222,
                'longitude' => 108.220556,
                'phone' => '+84-236-3847-333',
                'email' => 'reservation@danangbeach.vn',
                'website' => 'https://danangbeach.vn',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation up to 5 days before arrival.',
                'is_active' => true,
            ],

            // PHILIPPINES
            [
                'name' => 'Manila Bay Hotel',
                'description' => 'Premium hotel along Manila Bay with spectacular sunset views, rooftop bar, and proximity to historical sites.',
                'address' => 'Roxas Boulevard, Ermita',
                'city' => 'Manila',
                'state_province' => 'Metro Manila',
                'country' => 'Philippines',
                'latitude' => 14.583056,
                'longitude' => 120.980556,
                'phone' => '+63-2-8123-4567',
                'email' => 'info@manilabayhotel.ph',
                'website' => 'https://manilabayhotel.ph',
                'star_rating' => 4,
                'check_in_time' => '14:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation 48 hours before check-in.',
                'is_active' => true,
            ],
            [
                'name' => 'Boracay White Beach Resort',
                'description' => 'Tropical paradise resort on White Beach with crystal clear waters, water sports, and island hopping tours.',
                'address' => 'Station 1, White Beach',
                'city' => 'Boracay',
                'state_province' => 'Aklan',
                'country' => 'Philippines',
                'latitude' => 11.967500,
                'longitude' => 121.925278,
                'phone' => '+63-36-288-3456',
                'email' => 'reservation@boracaywhite.ph',
                'website' => 'https://boracaywhite.ph',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Full refund if cancelled 7 days prior to arrival.',
                'is_active' => true,
            ],

            // JAPAN
            [
                'name' => 'Tokyo Imperial Hotel',
                'description' => 'Historic luxury hotel in Chiyoda with traditional Japanese service, Michelin-starred restaurants, and modern facilities.',
                'address' => '1-1-1 Uchisaiwaicho, Chiyoda',
                'city' => 'Tokyo',
                'state_province' => 'Tokyo',
                'country' => 'Japan',
                'latitude' => 35.675556,
                'longitude' => 139.758889,
                'phone' => '+81-3-3504-1111',
                'email' => 'reservation@tokyoimperial.jp',
                'website' => 'https://tokyoimperial.jp',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation until 24 hours before arrival.',
                'is_active' => true,
            ],
            [
                'name' => 'Kyoto Traditional Ryokan',
                'description' => 'Authentic Japanese ryokan with tatami rooms, onsen hot springs, kaiseki dining, and zen garden views.',
                'address' => '459 Kiyomizu, Higashiyama',
                'city' => 'Kyoto',
                'state_province' => 'Kyoto',
                'country' => 'Japan',
                'latitude' => 34.994722,
                'longitude' => 135.785000,
                'phone' => '+81-75-561-0771',
                'email' => 'info@kyotoryokan.jp',
                'website' => 'https://kyotoryokan.jp',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '10:00:00',
                'cancellation_policy' => 'Cancellation must be made 7 days in advance for full refund.',
                'is_active' => true,
            ],

            // SOUTH KOREA
            [
                'name' => 'Seoul Gangnam Tower Hotel',
                'description' => 'Modern luxury hotel in Gangnam district with panoramic city views, K-spa, and proximity to shopping districts.',
                'address' => '123 Teheran-ro, Gangnam-gu',
                'city' => 'Seoul',
                'state_province' => 'Seoul',
                'country' => 'South Korea',
                'latitude' => 37.498889,
                'longitude' => 127.027778,
                'phone' => '+82-2-2112-3456',
                'email' => 'info@gangnamtower.kr',
                'website' => 'https://gangnamtower.kr',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation 48 hours before check-in.',
                'is_active' => true,
            ],
            [
                'name' => 'Jeju Island Ocean Resort',
                'description' => 'Stunning resort on Jeju Island with volcanic rock beach, natural hot springs, and traditional Korean wellness center.',
                'address' => '2039 Jungmun-dong, Seogwipo',
                'city' => 'Seogwipo',
                'state_province' => 'Jeju',
                'country' => 'South Korea',
                'latitude' => 33.244167,
                'longitude' => 126.411667,
                'phone' => '+82-64-738-7777',
                'email' => 'reservation@jejuocean.kr',
                'website' => 'https://jejuocean.kr',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Free cancellation until 72 hours before arrival.',
                'is_active' => true,
            ],

            // UNITED STATES
            [
                'name' => 'New York Manhattan Plaza',
                'description' => 'Iconic hotel in Times Square with Broadway theater access, rooftop bar, and stunning skyline views.',
                'address' => '123 West 44th Street, Midtown',
                'city' => 'New York',
                'state_province' => 'New York',
                'country' => 'United States',
                'latitude' => 40.755833,
                'longitude' => -73.986389,
                'phone' => '+1-212-555-1234',
                'email' => 'info@nymanhattanplaza.com',
                'website' => 'https://nymanhattanplaza.com',
                'star_rating' => 5,
                'check_in_time' => '16:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Free cancellation 48 hours prior to arrival.',
                'is_active' => true,
            ],
            [
                'name' => 'Las Vegas Grand Resort',
                'description' => 'Mega resort on the Strip with casino, multiple pools, world-class shows, and celebrity chef restaurants.',
                'address' => '3600 Las Vegas Boulevard South',
                'city' => 'Las Vegas',
                'state_province' => 'Nevada',
                'country' => 'United States',
                'latitude' => 36.114167,
                'longitude' => -115.172778,
                'phone' => '+1-702-693-7111',
                'email' => 'reservation@lvgrandresort.com',
                'website' => 'https://lvgrandresort.com',
                'star_rating' => 5,
                'check_in_time' => '16:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Cancellation allowed up to 72 hours before check-in.',
                'is_active' => true,
            ],

            // UNITED KINGDOM
            [
                'name' => 'London Westminster Hotel',
                'description' => 'Historic luxury hotel near Big Ben and Westminster Abbey with afternoon tea service and Victorian elegance.',
                'address' => '45 Park Lane, Westminster',
                'city' => 'London',
                'state_province' => 'England',
                'country' => 'United Kingdom',
                'latitude' => 51.501111,
                'longitude' => -0.145278,
                'phone' => '+44-20-7123-4567',
                'email' => 'info@londonwestminster.co.uk',
                'website' => 'https://londonwestminster.co.uk',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Free cancellation 24 hours before arrival.',
                'is_active' => true,
            ],

            // FRANCE
            [
                'name' => 'Paris Eiffel Tower Hotel',
                'description' => 'Boutique hotel with direct views of Eiffel Tower, French cuisine restaurant, and classic Parisian charm.',
                'address' => '32 Avenue de la Bourdonnais',
                'city' => 'Paris',
                'state_province' => 'Île-de-France',
                'country' => 'France',
                'latitude' => 48.858889,
                'longitude' => 2.294444,
                'phone' => '+33-1-45-55-55-55',
                'email' => 'reservation@pariseiffel.fr',
                'website' => 'https://pariseiffel.fr',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Annulation gratuite 48 heures avant l\'arrivée.',
                'is_active' => true,
            ],

            // AUSTRALIA
            [
                'name' => 'Sydney Harbour View Hotel',
                'description' => 'Premium hotel overlooking Sydney Opera House and Harbour Bridge with rooftop pool and fine dining.',
                'address' => '88 George Street, The Rocks',
                'city' => 'Sydney',
                'state_province' => 'New South Wales',
                'country' => 'Australia',
                'latitude' => -33.861389,
                'longitude' => 151.210556,
                'phone' => '+61-2-9250-6000',
                'email' => 'info@sydneyharbourview.com.au',
                'website' => 'https://sydneyharbourview.com.au',
                'star_rating' => 5,
                'check_in_time' => '14:00:00',
                'check_out_time' => '11:00:00',
                'cancellation_policy' => 'Free cancellation up to 48 hours before check-in.',
                'is_active' => true,
            ],

            // UNITED ARAB EMIRATES
            [
                'name' => 'Dubai Burj Khalifa Hotel',
                'description' => 'Ultra-luxury hotel in Downtown Dubai with views of Burj Khalifa, gold-plated fixtures, and butler service.',
                'address' => 'Sheikh Mohammed bin Rashid Blvd',
                'city' => 'Dubai',
                'state_province' => 'Dubai',
                'country' => 'United Arab Emirates',
                'latitude' => 25.197222,
                'longitude' => 55.274056,
                'phone' => '+971-4-888-3888',
                'email' => 'reservation@dubaiburjkhalifa.ae',
                'website' => 'https://dubaiburjkhalifa.ae',
                'star_rating' => 5,
                'check_in_time' => '15:00:00',
                'check_out_time' => '12:00:00',
                'cancellation_policy' => 'Free cancellation 72 hours before arrival.',
                'is_active' => true,
            ],
        ];

        DB::beginTransaction();

        try {
            foreach ($hotels as $hotelData) {
                // Create hotel
                $hotel = Hotel::create($hotelData);

                // Assign amenities (randomly select 5-8 amenities)
                $randomAmenityCount = rand(5, min(8, count($hotelAmenities)));
                $selectedAmenities = array_rand(array_flip($hotelAmenities), $randomAmenityCount);
                $hotel->amenities()->sync($selectedAmenities);

                // Add hotel images from storage (3-5 random images per hotel)
                $imageCount = rand(3, 5);
                for ($i = 0; $i < $imageCount; $i++) {
                    $randomImageNumber = rand(1, 10);
                    HotelImage::create([
                        'hotel_id' => $hotel->id,
                        'image_url' => "hotels/hotel{$randomImageNumber}.jpg",
                    ]);
                }

                // Assign owner if available
                if ($owners->isNotEmpty()) {
                    $owner = $owners[$ownerIndex % $owners->count()];
                    $hotel->update(['owner_id' => $owner->id]);
                    $ownerIndex++;
                }

                $this->command->info("Created: {$hotel->name} in {$hotel->city}, {$hotel->country}");
            }

            DB::commit();
            $this->command->info("\n✓ Successfully created " . count($hotels) . " hotels across " . collect($hotels)->pluck('country')->unique()->count() . " countries!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error: " . $e->getMessage());
            throw $e;
        }
    }
}
