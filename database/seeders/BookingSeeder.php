<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\User;
use App\Models\Receptionist;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all();
        $hotels = Hotel::all();
        $receptionists = Receptionist::all();

        foreach ($hotels as $hotel) {
            $rooms = Room::where('hotel_id', $hotel->id)->get();

            foreach ($rooms as $room) {
                for ($i = 0; $i < 3; $i++) {
                    // Tentukan apakah booking online atau offline
                    $isOnline = rand(0, 1) === 1;

                    $checkIn = Carbon::today()->addDays(rand(1, 30));
                    $checkOut = (clone $checkIn)->addDays(rand(1, 7));
                    $numDays = $checkOut->diffInDays($checkIn);
                    $totalPrice = $room->price_per_night * ($numDays > 0 ? $numDays : 1);

                    if ($isOnline) {
                        $user = $users->random();

                        // Pastikan guest_name dan guest_email tidak null
                        $guestName = $user->name ?? explode('@', $user->email)[0] ?? 'Guest_' . rand(1000, 9999);
                        $guestEmail = $user->email ?? 'guest' . rand(1000, 9999) . '@example.com';
                        $guestPhone = $user->phone ?? null;

                        DB::table('bookings')->insert([
                            'user_id' => $user->id,
                            'receptionist_id' => null,
                            'hotel_id' => $hotel->id,
                            'room_id' => $room->id,
                            'guest_name' => $guestName,
                            'guest_email' => $guestEmail,
                            'guest_phone' => $guestPhone,
                            'check_in_date' => $checkIn->toDateString(),
                            'check_out_date' => $checkOut->toDateString(),
                            'status' => ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled'][array_rand([0,1,2,3,4])],
                            'source' => 'online',
                            'total_price' => $totalPrice,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        // Booking offline via receptionist
                        $receptionist = $receptionists->random();

                        $guestName = 'Tamu ' . ucfirst(str()->random(5));
                        $guestEmail = 'guest' . rand(1, 1000) . '@example.com';
                        $guestPhone = '08' . rand(1000000000, 9999999999);

                        DB::table('bookings')->insert([
                            'user_id' => null,
                            'receptionist_id' => $receptionist->id,
                            'hotel_id' => $hotel->id,
                            'room_id' => $room->id,
                            'guest_name' => $guestName,
                            'guest_email' => $guestEmail,
                            'guest_phone' => $guestPhone,
                            'check_in_date' => $checkIn->toDateString(),
                            'check_out_date' => $checkOut->toDateString(),
                            'status' => 'booked',
                            'source' => 'offline',
                            'total_price' => $totalPrice,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
