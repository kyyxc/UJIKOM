<?php

namespace Database\Seeders;

use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AddReceptionistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = 'myreceptionist@example.com';
        if (User::where('email', $email)->exists()) {
            $this->command->info('User sudah ada, skip seeding.');
            return;
        }

        // Buat user baru
        $user = User::create([
            'email' => $email,
            'password' => Hash::make('password123'), // ganti sesuai kebutuhan
            'first_name' => 'NamaDepan',
            'last_name' => 'NamaBelakang',
            'address' => 'Alamat contoh',
            'date_of_birth' => '1995-01-01',
            'profile' => null,
        ]);

        // Buat data receptionist terkait hotel_id 2
        Receptionist::create([
            'user_id' => $user->id,
            'hotel_id' => 2,
        ]);

        $this->command->info('Receptionist baru berhasil dibuat.');
    }
}
