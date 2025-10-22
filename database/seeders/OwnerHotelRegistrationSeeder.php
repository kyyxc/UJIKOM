<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Owner;
use App\Models\Hotel;
use App\Models\Amenity;
use App\Models\HotelImage;
use App\Models\HotelAmenity;
use App\Models\Room;
use App\Models\RoomAmenity;
use App\Models\RoomImage;
use Carbon\Carbon;

class OwnerHotelRegistrationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder ini membuat berbagai owner dengan status registrasi yang berbeda:
     * 1. Owner dengan hotel APPROVED (aktif)
     * 2. Owner dengan hotel PENDING (menunggu approval)
     * 3. Owner dengan hotel REJECTED (ditolak)
     * 4. Owner yang baru Step 1 (akun saja)
     * 5. Owner yang baru Step 2 (hotel basic info)
     * 6. Owner yang baru Step 3 (amenities & photos)
     * 7. Owner yang baru Step 4 (banking & documents)
     */
    public function run(): void
    {
        // Ambil amenities berdasarkan type
        $hotelAmenities = Amenity::where('type', 'hotel')->pluck('id')->toArray();
        $roomAmenities = Amenity::where('type', 'room')->pluck('id')->toArray();

        // ==========================================
        // 1. OWNER APPROVED - 3 Hotels (Fully Operational)
        // ==========================================
        $this->createApprovedOwner(
            'john.doe@hotel.com',
            'John Doe',
            'Grand Luxury Hotel Jakarta',
            'Hotel bintang 5 di pusat kota Jakarta dengan fasilitas lengkap dan pemandangan kota yang menakjubkan.',
            'Jl. MH Thamrin No. 1',
            'Jakarta',
            'DKI Jakarta',
            '+6221123456',
            'Bank BCA',
            '1234567890',
            'John Doe',
            5,
            $hotelAmenities,
            $roomAmenities
        );

        $this->createApprovedOwner(
            'sarah.smith@hotel.com',
            'Sarah Smith',
            'Bali Beach Resort & Spa',
            'Resort mewah di tepi pantai Bali dengan pemandangan sunset yang indah dan layanan spa premium.',
            'Jl. Pantai Kuta No. 88',
            'Badung',
            'Bali',
            '+62361987654',
            'Bank Mandiri',
            '9876543210',
            'Sarah Smith',
            5,
            $hotelAmenities,
            $roomAmenities
        );

        $this->createApprovedOwner(
            'michael.tan@hotel.com',
            'Michael Tan',
            'Bandung Highland Hotel',
            'Hotel dengan pemandangan pegunungan yang sejuk dan nyaman, cocok untuk liburan keluarga.',
            'Jl. Raya Lembang No. 234',
            'Bandung',
            'Jawa Barat',
            '+62227654321',
            'Bank BNI',
            '5555666677',
            'Michael Tan',
            4,
            $hotelAmenities,
            $roomAmenities
        );

        // ==========================================
        // 2. OWNER PENDING - 2 Hotels (Waiting Admin Approval)
        // ==========================================
        $this->createPendingOwner(
            'david.lee@hotel.com',
            'David Lee',
            'Surabaya Business Hotel',
            'Hotel bisnis modern di pusat kota Surabaya dengan meeting room lengkap.',
            'Jl. Basuki Rahmat No. 45',
            'Surabaya',
            'Jawa Timur',
            '+62317771234',
            'Bank CIMB',
            '1111222233',
            'David Lee',
            4,
            $hotelAmenities,
            $roomAmenities
        );

        $this->createPendingOwner(
            'linda.wong@hotel.com',
            'Linda Wong',
            'Yogyakarta Cultural Inn',
            'Hotel dengan nuansa budaya Jogja yang kental, dekat dengan Malioboro dan Keraton.',
            'Jl. Malioboro No. 99',
            'Yogyakarta',
            'DI Yogyakarta',
            '+62274888999',
            'Bank BRI',
            '3333444455',
            'Linda Wong',
            3,
            $hotelAmenities,
            $roomAmenities
        );

        // ==========================================
        // 3. OWNER REJECTED - 1 Hotel (Rejected with Reason)
        // ==========================================
        $this->createRejectedOwner(
            'rejected.owner@hotel.com',
            'Robert Chen',
            'Rejected Hotel Example',
            'Hotel dengan dokumen yang tidak valid.',
            'Jl. Example No. 123',
            'Jakarta',
            'DKI Jakarta',
            '+6281234567890',
            'Bank XYZ',
            '9999888877',
            'Robert Chen',
            3,
            'Dokumen SIUP tidak valid. File yang diupload buram dan tidak terbaca. Mohon upload ulang dengan dokumen yang jelas dan valid. Pastikan nomor SIUP sesuai dengan yang tertera di dokumen.',
            $hotelAmenities,
            $roomAmenities
        );

        // ==========================================
        // 4. OWNER STEP 1 - Account Only (2 Owners)
        // ==========================================
        $this->createStep1Owner('step1.owner1@hotel.com', 'Alice Brown');
        $this->createStep1Owner('step1.owner2@hotel.com', 'Bob Wilson');

        // ==========================================
        // 5. OWNER STEP 2 - Hotel Basic Info (1 Owner)
        // ==========================================
        $this->createStep2Owner(
            'step2.owner@hotel.com',
            'Charlie Davis',
            'Semarang Downtown Hotel',
            'Hotel modern di pusat kota Semarang.',
            'Jl. Pandanaran No. 55',
            'Semarang',
            'Jawa Tengah',
            '+62243334455',
            3
        );

        // ==========================================
        // 6. OWNER STEP 3 - Amenities & Photos (1 Owner)
        // ==========================================
        $this->createStep3Owner(
            'step3.owner@hotel.com',
            'Emma Martinez',
            'Medan Paradise Hotel',
            'Hotel nyaman dengan fasilitas lengkap di Medan.',
            'Jl. Gatot Subroto No. 77',
            'Medan',
            'Sumatera Utara',
            '+62618887766',
            4,
            $hotelAmenities
        );

        // ==========================================
        // 7. OWNER STEP 4 - Banking & Documents (1 Owner)
        // ==========================================
        $this->createStep4Owner(
            'step4.owner@hotel.com',
            'Frank Robinson',
            'Makassar Coastal Hotel',
            'Hotel tepi pantai dengan pemandangan indah di Makassar.',
            'Jl. Pantai Losari No. 123',
            'Makassar',
            'Sulawesi Selatan',
            '+62415554433',
            'Bank Permata',
            '7777888899',
            'Frank Robinson',
            4,
            $hotelAmenities
        );

        $this->command->info('✅ Owner Hotel Registration Seeder completed successfully!');
        $this->command->info('   - 3 Approved Hotels');
        $this->command->info('   - 2 Pending Hotels');
        $this->command->info('   - 1 Rejected Hotel');
        $this->command->info('   - 2 Step 1 Owners (Account Only)');
        $this->command->info('   - 1 Step 2 Owner (Basic Info)');
        $this->command->info('   - 1 Step 3 Owner (Amenities & Photos)');
        $this->command->info('   - 1 Step 4 Owner (Banking & Documents)');
    }

    /**
     * Create APPROVED owner with complete hotel
     */
    private function createApprovedOwner(
        string $email,
        string $name,
        string $hotelName,
        string $description,
        string $address,
        string $city,
        string $stateProvince,
        string $phone,
        string $bankName,
        string $accountNumber,
        string $accountHolder,
        int $starRating,
        array $hotelAmenities,
        array $roomAmenities
    ): void {
        // Create User
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => true,
        ]);

        // Create Hotel
        $hotel = Hotel::create([
            'name' => $hotelName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state_province' => $stateProvince,
            'country' => 'Indonesia',
            'latitude' => -6.200000 + (rand(1, 100) * 0.001),
            'longitude' => 106.816666 + (rand(1, 100) * 0.001),
            'email' => $email,
            'phone' => $phone,
            'website' => 'https://' . str_replace(' ', '', strtolower($hotelName)) . '.com',
            'star_rating' => $starRating,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'cancellation_policy' => 'Pembatalan gratis hingga 24 jam sebelum check-in. Setelah itu akan dikenakan biaya 50%.',
            'is_active' => true,
        ]);

        // Create Owner with APPROVED status
        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_holder_name' => $accountHolder,
            'business_license_number' => 'SIUP-' . rand(100000, 999999),
            'business_license_file' => 'documents/siup_' . time() . '_' . rand(1000, 9999) . '.pdf',
            'tax_id_number' => 'NPWP-' . rand(1000000000, 9999999999),
            'tax_id_file' => 'documents/npwp_' . time() . '_' . rand(1000, 9999) . '.pdf',
            'identity_card_file' => 'documents/ktp_' . time() . '_' . rand(1000, 9999) . '.pdf',
            'registration_status' => 'approved',
            'rejection_reason' => null,
            'submitted_at' => Carbon::now()->subDays(rand(10, 30)),
            'approved_at' => Carbon::now()->subDays(rand(1, 9)),
        ]);

        // Add Hotel Amenities (random 3-5 amenities)
        $selectedAmenities = array_rand(array_flip($hotelAmenities), rand(3, min(5, count($hotelAmenities))));
        foreach ((array)$selectedAmenities as $amenityId) {
            HotelAmenity::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenityId,
            ]);
        }

        // Add Hotel Images (3-5 images)
        for ($i = 1; $i <= rand(3, 5); $i++) {
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => 'hotels/hotel' . rand(1, 10) . '.jpg',
            ]);
        }

        // Create Rooms (4 types)
        $this->createRooms($hotel->id, $roomAmenities);
    }

    /**
     * Create PENDING owner with complete hotel (waiting approval)
     */
    private function createPendingOwner(
        string $email,
        string $name,
        string $hotelName,
        string $description,
        string $address,
        string $city,
        string $stateProvince,
        string $phone,
        string $bankName,
        string $accountNumber,
        string $accountHolder,
        int $starRating,
        array $hotelAmenities,
        array $roomAmenities
    ): void {
        // Create User (inactive until approved)
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => false,
        ]);

        // Create Hotel (inactive until approved)
        $hotel = Hotel::create([
            'name' => $hotelName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state_province' => $stateProvince,
            'country' => 'Indonesia',
            'latitude' => -6.200000 + (rand(1, 100) * 0.001),
            'longitude' => 106.816666 + (rand(1, 100) * 0.001),
            'email' => $email,
            'phone' => $phone,
            'website' => 'https://' . str_replace(' ', '', strtolower($hotelName)) . '.com',
            'star_rating' => $starRating,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'cancellation_policy' => 'Pembatalan gratis hingga 24 jam sebelum check-in.',
            'is_active' => false,
        ]);

        // Create Owner with COMPLETED status (pending approval)
        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_holder_name' => $accountHolder,
            'business_license_number' => 'SIUP-' . rand(100000, 999999),
            'business_license_file' => 'documents/siup_' . time() . '_' . rand(1000, 9999) . '.pdf',
            'tax_id_number' => 'NPWP-' . rand(1000000000, 9999999999),
            'tax_id_file' => 'documents/npwp_' . time() . '_' . rand(1000, 9999) . '.pdf',
            'identity_card_file' => 'documents/ktp_' . time() . '_' . rand(1000, 9999) . '.pdf',
            'registration_status' => 'completed',
            'rejection_reason' => null,
            'submitted_at' => Carbon::now()->subDays(rand(1, 7)),
            'approved_at' => null,
        ]);

        // Add Hotel Amenities
        $selectedAmenities = array_rand(array_flip($hotelAmenities), rand(3, min(5, count($hotelAmenities))));
        foreach ((array)$selectedAmenities as $amenityId) {
            HotelAmenity::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenityId,
            ]);
        }

        // Add Hotel Images
        for ($i = 1; $i <= rand(3, 5); $i++) {
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => 'hotels/hotel' . rand(1, 10) . '.jpg',
            ]);
        }

        // Create Rooms
        $this->createRooms($hotel->id, $roomAmenities);
    }

    /**
     * Create REJECTED owner with complete hotel
     */
    private function createRejectedOwner(
        string $email,
        string $name,
        string $hotelName,
        string $description,
        string $address,
        string $city,
        string $stateProvince,
        string $phone,
        string $bankName,
        string $accountNumber,
        string $accountHolder,
        int $starRating,
        string $rejectionReason,
        array $hotelAmenities,
        array $roomAmenities
    ): void {
        // Create User (inactive)
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => false,
        ]);

        // Create Hotel (inactive)
        $hotel = Hotel::create([
            'name' => $hotelName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state_province' => $stateProvince,
            'country' => 'Indonesia',
            'latitude' => -6.200000 + (rand(1, 100) * 0.001),
            'longitude' => 106.816666 + (rand(1, 100) * 0.001),
            'email' => $email,
            'phone' => $phone,
            'website' => null,
            'star_rating' => $starRating,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'cancellation_policy' => 'Pembatalan gratis hingga 24 jam sebelum check-in.',
            'is_active' => false,
        ]);

        // Create Owner with REJECTED status
        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_holder_name' => $accountHolder,
            'business_license_number' => 'SIUP-' . rand(100000, 999999),
            'business_license_file' => 'documents/siup_invalid.pdf',
            'tax_id_number' => 'NPWP-' . rand(1000000000, 9999999999),
            'tax_id_file' => 'documents/npwp_' . time() . '.pdf',
            'identity_card_file' => 'documents/ktp_' . time() . '.pdf',
            'registration_status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'submitted_at' => Carbon::now()->subDays(rand(5, 15)),
            'approved_at' => null,
        ]);

        // Add minimal amenities
        $selectedAmenities = array_rand(array_flip($hotelAmenities), rand(2, 3));
        foreach ((array)$selectedAmenities as $amenityId) {
            HotelAmenity::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenityId,
            ]);
        }

        // Add minimal images
        for ($i = 1; $i <= 2; $i++) {
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => 'hotels/hotel' . rand(1, 10) . '.jpg',
            ]);
        }
    }

    /**
     * Create STEP 1 owner (account only)
     */
    private function createStep1Owner(string $email, string $name): void
    {
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => false,
        ]);

        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => null,
            'registration_status' => 'step_1',
            'submitted_at' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Create STEP 2 owner (hotel basic info)
     */
    private function createStep2Owner(
        string $email,
        string $name,
        string $hotelName,
        string $description,
        string $address,
        string $city,
        string $stateProvince,
        string $phone,
        int $starRating
    ): void {
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => false,
        ]);

        $hotel = Hotel::create([
            'name' => $hotelName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state_province' => $stateProvince,
            'country' => 'Indonesia',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'email' => $email,
            'phone' => $phone,
            'star_rating' => $starRating,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'is_active' => false,
        ]);

        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'registration_status' => 'step_2',
            'submitted_at' => null,
            'approved_at' => null,
        ]);
    }

    /**
     * Create STEP 3 owner (amenities & photos)
     */
    private function createStep3Owner(
        string $email,
        string $name,
        string $hotelName,
        string $description,
        string $address,
        string $city,
        string $stateProvince,
        string $phone,
        int $starRating,
        array $hotelAmenities
    ): void {
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => false,
        ]);

        $hotel = Hotel::create([
            'name' => $hotelName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state_province' => $stateProvince,
            'country' => 'Indonesia',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'email' => $email,
            'phone' => $phone,
            'star_rating' => $starRating,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'is_active' => false,
        ]);

        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'registration_status' => 'step_3',
            'submitted_at' => null,
            'approved_at' => null,
        ]);

        // Add some amenities
        $selectedAmenities = array_rand(array_flip($hotelAmenities), rand(2, 4));
        foreach ((array)$selectedAmenities as $amenityId) {
            HotelAmenity::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenityId,
            ]);
        }

        // Add some images
        // Add some images
        for ($i = 1; $i <= 3; $i++) {
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => 'hotels/hotel' . rand(1, 10) . '.jpg',
            ]);
        }
    }

    /**
     * Create STEP 4 owner (banking & documents)
     */
    private function createStep4Owner(
        string $email,
        string $name,
        string $hotelName,
        string $description,
        string $address,
        string $city,
        string $stateProvince,
        string $phone,
        string $bankName,
        string $accountNumber,
        string $accountHolder,
        int $starRating,
        array $hotelAmenities
    ): void {
        $nameParts = $this->splitName($name);
        $user = User::create([
            'first_name' => $nameParts['first_name'],
            'last_name' => $nameParts['last_name'],
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'owner',
            'is_active' => false,
        ]);

        $hotel = Hotel::create([
            'name' => $hotelName,
            'description' => $description,
            'address' => $address,
            'city' => $city,
            'state_province' => $stateProvince,
            'country' => 'Indonesia',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'email' => $email,
            'phone' => $phone,
            'star_rating' => $starRating,
            'check_in_time' => '14:00:00',
            'check_out_time' => '12:00:00',
            'is_active' => false,
        ]);

        Owner::create([
            'user_id' => $user->id,
            'hotel_id' => $hotel->id,
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_holder_name' => $accountHolder,
            'business_license_number' => 'SIUP-' . rand(100000, 999999),
            'business_license_file' => 'documents/siup_' . time() . '.pdf',
            'tax_id_number' => 'NPWP-' . rand(1000000000, 9999999999),
            'tax_id_file' => 'documents/npwp_' . time() . '.pdf',
            'identity_card_file' => 'documents/ktp_' . time() . '.pdf',
            'registration_status' => 'step_4',
            'submitted_at' => null,
            'approved_at' => null,
        ]);

        // Add amenities
        $selectedAmenities = array_rand(array_flip($hotelAmenities), rand(3, 5));
        foreach ((array)$selectedAmenities as $amenityId) {
            HotelAmenity::create([
                'hotel_id' => $hotel->id,
                'amenity_id' => $amenityId,
            ]);
        }

        // Add images
        // Add images
        for ($i = 1; $i <= 4; $i++) {
            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_url' => 'hotels/hotel' . rand(1, 10) . '.jpg',
            ]);
        }
    }

    /**
     * Create rooms for a hotel
     */
    private function createRooms(int $hotelId, array $roomAmenities): void
    {
        $roomTypes = [
            ['type' => 'single', 'capacity' => 1, 'price' => 500000],
            ['type' => 'double', 'capacity' => 2, 'price' => 750000],
            ['type' => 'deluxe', 'capacity' => 3, 'price' => 1200000],
            ['type' => 'suite', 'capacity' => 4, 'price' => 2500000],
        ];

        foreach ($roomTypes as $index => $roomType) {
            // Create 2-3 rooms for each type
            for ($i = 1; $i <= rand(2, 3); $i++) {
                $roomNumber = ($index + 1) . '0' . $i;

                $room = Room::create([
                    'hotel_id' => $hotelId,
                    'room_number' => $roomNumber,
                    'room_type' => $roomType['type'],
                    'description' => "Kamar tipe {$roomType['type']} yang nyaman dengan fasilitas modern.",
                    'capacity' => $roomType['capacity'],
                    'price_per_night' => $roomType['price'],
                    'status' => 'available',
                ]);

                // Add room image
                RoomImage::create([
                    'room_id' => $room->id,
                    'image_url' => 'rooms/room' . rand(1, 10) . '.jpg',
                ]);

                // Add room amenities (random 2-4 amenities)
                if (!empty($roomAmenities)) {
                    $selectedRoomAmenities = array_rand(array_flip($roomAmenities), min(rand(2, 4), count($roomAmenities)));
                    foreach ((array)$selectedRoomAmenities as $amenityId) {
                        RoomAmenity::create([
                            'room_id' => $room->id,
                            'amenity_id' => $amenityId,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Helper function to split full name into first and last name
     */
    private function splitName(string $fullName): array
    {
        $parts = explode(' ', $fullName, 2);
        return [
            'first_name' => $parts[0],
            'last_name' => $parts[1] ?? '',
        ];
    }
}
