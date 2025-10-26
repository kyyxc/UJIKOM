<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Receptionist;
use App\Models\Hotel;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // CREATE ADMIN USERS
        // ==========================================
        
        // Admin 1 - Super Admin
        $admin1 = User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@hotel.com',
            'phone' => '081234567890',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Admin::create([
            'user_id' => $admin1->id,
            'employee_id' => 'ADM001',
            'department' => 'Management',
            'position' => 'Super Admin',
        ]);

        // Admin 2
        $admin2 = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Johnson',
            'email' => 'sarah.admin@hotel.com',
            'phone' => '081234567891',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Admin::create([
            'user_id' => $admin2->id,
            'employee_id' => 'ADM002',
            'department' => 'Operations',
            'position' => 'Operations Manager',
        ]);

        // Admin 3
        $admin3 = User::create([
            'first_name' => 'Michael',
            'last_name' => 'Brown',
            'email' => 'michael.admin@hotel.com',
            'phone' => '081234567892',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        Admin::create([
            'user_id' => $admin3->id,
            'employee_id' => 'ADM003',
            'department' => 'Finance',
            'position' => 'Finance Manager',
        ]);

        // ==========================================
        // GET HOTELS FOR OWNER & RECEPTIONIST
        // ==========================================
        
        // Get first 5 hotels from database
        $hotels = Hotel::limit(5)->get();

        if ($hotels->count() < 1) {
            $this->command->warn('No hotels found. Please run HotelSeeder first!');
            return;
        }

        // ==========================================
        // CREATE OWNER & RECEPTIONIST FOR EACH HOTEL
        // ==========================================

        // Hotel 1 - Owner & Receptionist
        if (isset($hotels[0])) {
            // Owner 1
            $ownerUser1 = User::create([
                'first_name' => 'Robert',
                'last_name' => 'Anderson',
                'email' => 'robert.owner@hotel.com',
                'phone' => '081234560001',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);

            Owner::create([
                'user_id' => $ownerUser1->id,
                'hotel_id' => $hotels[0]->id,
                'business_license_number' => 'BLN-2024-001',
                'bank_account_number' => '1234567890',
                'bank_name' => 'Bank Mandiri',
                'bank_account_holder' => 'Robert Anderson',
                'registration_status' => 'approved',
                'registration_step' => 5,
                'approved_at' => now(),
                'approved_by' => $admin1->id,
            ]);

            // Receptionist 1 (Same Hotel as Owner 1)
            $receptionistUser1 = User::create([
                'first_name' => 'Lisa',
                'last_name' => 'Martinez',
                'email' => 'lisa.receptionist@hotel.com',
                'phone' => '081234561001',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser1->id,
                'hotel_id' => $hotels[0]->id,
                'employee_id' => 'RCP001',
                'shift' => 'morning',
            ]);

            // Receptionist 2 (Same Hotel as Owner 1)
            $receptionistUser2 = User::create([
                'first_name' => 'David',
                'last_name' => 'Wilson',
                'email' => 'david.receptionist@hotel.com',
                'phone' => '081234561002',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser2->id,
                'hotel_id' => $hotels[0]->id,
                'employee_id' => 'RCP002',
                'shift' => 'afternoon',
            ]);
        }

        // Hotel 2 - Owner & Receptionist
        if (isset($hotels[1])) {
            // Owner 2
            $ownerUser2 = User::create([
                'first_name' => 'Jennifer',
                'last_name' => 'Taylor',
                'email' => 'jennifer.owner@hotel.com',
                'phone' => '081234560002',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);

            Owner::create([
                'user_id' => $ownerUser2->id,
                'hotel_id' => $hotels[1]->id,
                'business_license_number' => 'BLN-2024-002',
                'bank_account_number' => '0987654321',
                'bank_name' => 'BCA',
                'bank_account_holder' => 'Jennifer Taylor',
                'registration_status' => 'approved',
                'registration_step' => 5,
                'approved_at' => now(),
                'approved_by' => $admin1->id,
            ]);

            // Receptionist 3 (Same Hotel as Owner 2)
            $receptionistUser3 = User::create([
                'first_name' => 'Emily',
                'last_name' => 'Thompson',
                'email' => 'emily.receptionist@hotel.com',
                'phone' => '081234561003',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser3->id,
                'hotel_id' => $hotels[1]->id,
                'employee_id' => 'RCP003',
                'shift' => 'morning',
            ]);

            // Receptionist 4 (Same Hotel as Owner 2)
            $receptionistUser4 = User::create([
                'first_name' => 'James',
                'last_name' => 'Garcia',
                'email' => 'james.receptionist@hotel.com',
                'phone' => '081234561004',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser4->id,
                'hotel_id' => $hotels[1]->id,
                'employee_id' => 'RCP004',
                'shift' => 'night',
            ]);
        }

        // Hotel 3 - Owner & Receptionist
        if (isset($hotels[2])) {
            // Owner 3
            $ownerUser3 = User::create([
                'first_name' => 'William',
                'last_name' => 'Davis',
                'email' => 'william.owner@hotel.com',
                'phone' => '081234560003',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);

            Owner::create([
                'user_id' => $ownerUser3->id,
                'hotel_id' => $hotels[2]->id,
                'business_license_number' => 'BLN-2024-003',
                'bank_account_number' => '1122334455',
                'bank_name' => 'BNI',
                'bank_account_holder' => 'William Davis',
                'registration_status' => 'approved',
                'registration_step' => 5,
                'approved_at' => now(),
                'approved_by' => $admin1->id,
            ]);

            // Receptionist 5 (Same Hotel as Owner 3)
            $receptionistUser5 = User::create([
                'first_name' => 'Sophia',
                'last_name' => 'Rodriguez',
                'email' => 'sophia.receptionist@hotel.com',
                'phone' => '081234561005',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser5->id,
                'hotel_id' => $hotels[2]->id,
                'employee_id' => 'RCP005',
                'shift' => 'morning',
            ]);
        }

        // Hotel 4 - Owner & Receptionist
        if (isset($hotels[3])) {
            // Owner 4
            $ownerUser4 = User::create([
                'first_name' => 'Daniel',
                'last_name' => 'Martinez',
                'email' => 'daniel.owner@hotel.com',
                'phone' => '081234560004',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);

            Owner::create([
                'user_id' => $ownerUser4->id,
                'hotel_id' => $hotels[3]->id,
                'business_license_number' => 'BLN-2024-004',
                'bank_account_number' => '5544332211',
                'bank_name' => 'BRI',
                'bank_account_holder' => 'Daniel Martinez',
                'registration_status' => 'approved',
                'registration_step' => 5,
                'approved_at' => now(),
                'approved_by' => $admin1->id,
            ]);

            // Receptionist 6 (Same Hotel as Owner 4)
            $receptionistUser6 = User::create([
                'first_name' => 'Olivia',
                'last_name' => 'Hernandez',
                'email' => 'olivia.receptionist@hotel.com',
                'phone' => '081234561006',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser6->id,
                'hotel_id' => $hotels[3]->id,
                'employee_id' => 'RCP006',
                'shift' => 'afternoon',
            ]);
        }

        // Hotel 5 - Owner & Receptionist
        if (isset($hotels[4])) {
            // Owner 5
            $ownerUser5 = User::create([
                'first_name' => 'Christopher',
                'last_name' => 'Lopez',
                'email' => 'christopher.owner@hotel.com',
                'phone' => '081234560005',
                'password' => Hash::make('password123'),
                'role' => 'owner',
                'email_verified_at' => now(),
            ]);

            Owner::create([
                'user_id' => $ownerUser5->id,
                'hotel_id' => $hotels[4]->id,
                'business_license_number' => 'BLN-2024-005',
                'bank_account_number' => '9988776655',
                'bank_name' => 'CIMB Niaga',
                'bank_account_holder' => 'Christopher Lopez',
                'registration_status' => 'approved',
                'registration_step' => 5,
                'approved_at' => now(),
                'approved_by' => $admin1->id,
            ]);

            // Receptionist 7 (Same Hotel as Owner 5)
            $receptionistUser7 = User::create([
                'first_name' => 'Ava',
                'last_name' => 'Gonzalez',
                'email' => 'ava.receptionist@hotel.com',
                'phone' => '081234561007',
                'password' => Hash::make('password123'),
                'role' => 'receptionist',
                'email_verified_at' => now(),
            ]);

            Receptionist::create([
                'user_id' => $receptionistUser7->id,
                'hotel_id' => $hotels[4]->id,
                'employee_id' => 'RCP007',
                'shift' => 'night',
            ]);
        }

        // ==========================================
        // CREATE REGULAR USERS (GUESTS)
        // ==========================================

        // Guest 1
        User::create([
            'first_name' => 'John',
            'last_name' => 'Guest',
            'email' => 'john.guest@example.com',
            'phone' => '081234570001',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Guest 2
        User::create([
            'first_name' => 'Jane',
            'last_name' => 'Customer',
            'email' => 'jane.customer@example.com',
            'phone' => '081234570002',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Guest 3
        User::create([
            'first_name' => 'Alex',
            'last_name' => 'Traveler',
            'email' => 'alex.traveler@example.com',
            'phone' => '081234570003',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ User Role Seeder completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('  - Admins: 3');
        $this->command->info('  - Owners: ' . Owner::count());
        $this->command->info('  - Receptionists: ' . Receptionist::count());
        $this->command->info('  - Regular Users: 3');
        $this->command->info('');
        $this->command->info('🔐 All passwords: password123');
        $this->command->info('');
        $this->command->info('👤 Login Credentials:');
        $this->command->info('');
        $this->command->info('ADMIN:');
        $this->command->info('  admin@hotel.com / password123');
        $this->command->info('  sarah.admin@hotel.com / password123');
        $this->command->info('  michael.admin@hotel.com / password123');
        $this->command->info('');
        $this->command->info('OWNER:');
        $this->command->info('  robert.owner@hotel.com / password123 (Hotel: ' . ($hotels[0]->name ?? 'N/A') . ')');
        $this->command->info('  jennifer.owner@hotel.com / password123 (Hotel: ' . ($hotels[1]->name ?? 'N/A') . ')');
        $this->command->info('  william.owner@hotel.com / password123 (Hotel: ' . ($hotels[2]->name ?? 'N/A') . ')');
        $this->command->info('');
        $this->command->info('RECEPTIONIST:');
        $this->command->info('  lisa.receptionist@hotel.com / password123 (Hotel: ' . ($hotels[0]->name ?? 'N/A') . ')');
        $this->command->info('  david.receptionist@hotel.com / password123 (Hotel: ' . ($hotels[0]->name ?? 'N/A') . ')');
        $this->command->info('  emily.receptionist@hotel.com / password123 (Hotel: ' . ($hotels[1]->name ?? 'N/A') . ')');
        $this->command->info('');
        $this->command->info('GUEST:');
        $this->command->info('  john.guest@example.com / password123');
        $this->command->info('  jane.customer@example.com / password123');
    }
}
