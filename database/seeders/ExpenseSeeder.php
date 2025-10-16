<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Expense;
use App\Models\Hotel;
use Carbon\Carbon;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotels = Hotel::all();

        foreach ($hotels as $hotel) {
            // Gaji karyawan
            Expense::create([
                'hotel_id' => $hotel->id,
                'category' => 'Gaji',
                'amount' => 15000000,
                'description' => 'Gaji karyawan bulan Oktober 2025',
                'date' => Carbon::now()->subDays(9),
                'payment_method' => 'Transfer Bank',
                'receipt_number' => 'PAYROLL-OCT-2025',
            ]);

            // Operasional
            Expense::create([
                'hotel_id' => $hotel->id,
                'category' => 'Operasional',
                'amount' => 5000000,
                'description' => 'Pembelian perlengkapan hotel (handuk, sprei, amenities)',
                'date' => Carbon::now()->subDays(4),
                'payment_method' => 'Transfer Bank',
                'receipt_number' => 'INV-2025-001',
            ]);

            // Maintenance
            Expense::create([
                'hotel_id' => $hotel->id,
                'category' => 'Maintenance',
                'amount' => 3000000,
                'description' => 'Perbaikan AC kamar 201-210 dan perbaikan plumbing',
                'date' => Carbon::now()->subDays(11),
                'payment_method' => 'Cash',
                'receipt_number' => 'MAINT-2025-015',
            ]);

            // Utilitas
            Expense::create([
                'hotel_id' => $hotel->id,
                'category' => 'Utilitas',
                'amount' => 2500000,
                'description' => 'Pembayaran listrik dan air bulan September',
                'date' => Carbon::now()->subDays(13),
                'payment_method' => 'Transfer Bank',
                'receipt_number' => 'UTIL-SEP-2025',
            ]);

            // Marketing
            Expense::create([
                'hotel_id' => $hotel->id,
                'category' => 'Marketing',
                'amount' => 1500000,
                'description' => 'Biaya iklan online dan promosi media sosial',
                'date' => Carbon::now()->subDays(16),
                'payment_method' => 'Credit Card',
                'receipt_number' => 'MKT-2025-009',
            ]);

            // Supplies
            Expense::create([
                'hotel_id' => $hotel->id,
                'category' => 'Supplies',
                'amount' => 1200000,
                'description' => 'Pembelian alat kebersihan dan supplies dapur',
                'date' => Carbon::now()->subDays(7),
                'payment_method' => 'Cash',
                'receipt_number' => 'SUP-2025-023',
            ]);
        }
    }
}
