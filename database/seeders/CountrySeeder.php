<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Indonesia',
                'code' => 'ID',
                'description' => 'Indonesia is an archipelago nation of diverse landscapes and cultures.',
                'image' => 'https://images.pexels.com/photos/2166553/pexels-photo-2166553.jpeg',
                'is_active' => true,
            ],
            [
                'name' => 'Malaysia',
                'code' => 'MY',
                'description' => 'Malaysia is a Southeast Asian country known for its beaches, rainforests and mix of Malay, Chinese, Indian and European cultural influences.',
                'image' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Singapore',
                'code' => 'SG',
                'description' => 'Singapore is a sunny, tropical island in Southeast Asia, off the southern tip of Malaysia.',
                'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Thailand',
                'code' => 'TH',
                'description' => 'Thailand is a Southeast Asian country known for tropical beaches, opulent royal palaces, ancient ruins and ornate temples.',
                'image' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Philippines',
                'code' => 'PH',
                'description' => 'The Philippines is a Southeast Asian country in the Western Pacific, comprising more than 7,000 islands.',
                'image' => 'https://images.unsplash.com/photo-1551244072-5d12893278ab?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Vietnam',
                'code' => 'VN',
                'description' => 'Vietnam is a Southeast Asian country known for its beaches, rivers, Buddhist pagodas and bustling cities.',
                'image' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Japan',
                'code' => 'JP',
                'description' => 'Japan is an island country in East Asia, known for its rich culture, technology and cuisine.',
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'South Korea',
                'code' => 'KR',
                'description' => 'South Korea is an East Asian nation on the southern half of the Korean Peninsula, known for its technology and pop culture.',
                'image' => 'https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'China',
                'code' => 'CN',
                'description' => 'China is an East Asian country with a rich history spanning thousands of years.',
                'image' => 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'United States',
                'code' => 'US',
                'description' => 'The United States is a large country in North America known for its diverse landscapes and culture.',
                'image' => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=800',
                'is_active' => true,
            ],
            // European Countries
            [
                'name' => 'France',
                'code' => 'FR',
                'description' => 'France is a Western European country known for its art, fashion, gastronomy and the iconic Eiffel Tower.',
                'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'description' => 'The United Kingdom comprises England, Scotland, Wales and Northern Ireland, known for its rich history and cultural heritage.',
                'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Germany',
                'code' => 'DE',
                'description' => 'Germany is a Western European country known for its history, beer, castles and the Brandenburg Gate.',
                'image' => 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Italy',
                'code' => 'IT',
                'description' => 'Italy is a European country with a long Mediterranean coastline, known for its Renaissance art, architecture and cuisine.',
                'image' => 'https://images.unsplash.com/photo-1523906834658-6e24ef2386f9?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Spain',
                'code' => 'ES',
                'description' => 'Spain is a European country known for its art, architecture, passionate culture and Mediterranean beaches.',
                'image' => 'https://images.unsplash.com/photo-1543783207-ec64e4d95325?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Netherlands',
                'code' => 'NL',
                'description' => 'The Netherlands is a Western European country known for its tulips, windmills, canals and cycling routes.',
                'image' => 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Switzerland',
                'code' => 'CH',
                'description' => 'Switzerland is a mountainous Central European country, known for its Alps, chocolate, watches and banking.',
                'image' => 'https://images.unsplash.com/photo-1527668752968-14dc70a27c95?w=800',
                'is_active' => true,
            ],
            [
                'name' => 'Austria',
                'code' => 'AT',
                'description' => 'Austria is a German-speaking country in Central Europe, known for its mountain villages, baroque architecture and musical legacy.',
                'image' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=800',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::create($country);
        }

        $this->command->info('Countries seeded successfully!');
    }
}
