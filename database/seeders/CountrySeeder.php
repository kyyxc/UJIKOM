<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

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
                'description' => 'Destinasi tropis dengan pantai eksotis dan budaya kaya',
                'image' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Singapore',
                'code' => 'SG',
                'description' => 'Kota modern dengan pelayanan kelas dunia',
                'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Malaysia',
                'code' => 'MY',
                'description' => 'Perpaduan sempurna tradisi dan modernitas',
                'image' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Thailand',
                'code' => 'TH',
                'description' => 'Kuil megah, pantai indah, dan kuliner lezat',
                'image' => 'https://images.unsplash.com/photo-1528181304800-259b08848526?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Japan',
                'code' => 'JP',
                'description' => 'Teknologi canggih bertemu tradisi kuno',
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'South Korea',
                'code' => 'KR',
                'description' => 'K-culture, teknologi, dan keindahan alam',
                'image' => 'https://images.unsplash.com/photo-1517154421773-0529f29ea451?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'United Arab Emirates',
                'code' => 'AE',
                'description' => 'Kemewahan gurun dengan arsitektur futuristik',
                'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Australia',
                'code' => 'AU',
                'description' => 'Keajaiban alam dari pantai hingga outback',
                'image' => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Vietnam',
                'code' => 'VN',
                'description' => 'Warisan budaya, lanskap indah, dan kuliner street food terbaik',
                'image' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'Philippines',
                'code' => 'PH',
                'description' => 'Pulau tropis dengan pantai putih dan keramahan penduduk',
                'image' => 'https://images.unsplash.com/photo-1584646098378-0874589d76b1?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'United States',
                'code' => 'US',
                'description' => 'Negara dengan keragaman budaya dan landmark ikonik dunia',
                'image' => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'United Kingdom',
                'code' => 'GB',
                'description' => 'Sejarah kaya, arsitektur klasik, dan budaya kontemporer',
                'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
            [
                'name' => 'France',
                'code' => 'FR',
                'description' => 'Seni, mode, kuliner, dan romantisme Eropa',
                'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&h=600&fit=crop',
                'is_active' => true,
            ],
        ];

        foreach ($countries as $country) {
            Country::firstOrCreate(
                ['code' => $country['code']],
                $country
            );
        }

        $this->command->info('✓ Successfully seeded ' . count($countries) . ' countries!');
    }
}
