<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Invoice;
use Carbon\Carbon;
use Faker\Factory as Faker;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get customers (users without admin/owner/receptionist roles)
        $customers = User::whereDoesntHave('admin')
            ->whereDoesntHave('owner')
            ->whereDoesntHave('receptionist')
            ->get();

        $rooms = Room::all();

        $statuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'];
        $paymentMethods = ['credit_card', 'bank_transfer', 'qris', 'gopay', 'shopeepay'];

        // Create 50 bookings
        for ($i = 1; $i <= 50; $i++) {
            $customer = $customers->random();
            $room = $rooms->random();
            
            // Random dates
            $checkInDate = $faker->dateTimeBetween('-30 days', '+30 days');
            $checkOutDate = Carbon::instance($checkInDate)->addDays(rand(2, 7));
            
            $totalNights = Carbon::instance($checkInDate)->diffInDays($checkOutDate);
            $totalPrice = $room->price_per_night * $totalNights;

            $booking = Booking::create([
                'user_id' => $customer->id,
                'room_id' => $room->id,
                'hotel_id' => $room->hotel_id,
                'guest_name' => $faker->name(),
                'guest_email' => $customer->email,
                'guest_phone' => $faker->phoneNumber,
                'check_in_date' => $checkInDate,
                'check_out_date' => $checkOutDate,
                'total_price' => $totalPrice,
                'status' => $faker->randomElement($statuses),
                'source' => 'online',
            ]);

            // Create payment for confirmed/checked_in/checked_out bookings
            if (in_array($booking->status, ['confirmed', 'checked_in', 'checked_out'])) {
                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $totalPrice,
                    'status' => 'paid',
                    'midtrans_order_id' => 'ORDER-' . now()->format('Ymd') . '-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'midtrans_transaction_id' => 'MIDTRANS-' . strtoupper($faker->bothify('???###???###')),
                    'midtrans_payment_type' => $faker->randomElement($paymentMethods),
                    'midtrans_response' => json_encode(['status' => 'success']),
                    'transaction_date' => $checkInDate,
                ]);

                // Create invoice
                Invoice::create([
                    'booking_id' => $booking->id,
                    'payment_id' => $payment->id,
                    'invoice_number' => 'INV-' . now()->format('Ymd') . '-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                    'amount' => $totalPrice,
                    'invoice_date' => $checkInDate,
                ]);
            }

            if ($i % 10 == 0) {
                $this->command->info("Booking {$i} created");
            }
        }

        $this->command->info('Bookings seeded successfully!');
    }
}
