<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReceptionistBookingStatusController extends Controller
{
    public function checkedIn(Request $request)
    {
        $receptionist = $request->user()->receptionist;

        if (!$receptionist) {
            return response()->json(['message' => 'Anda bukan receptionist'], 403);
        }

        $bookings = Booking::with('room')
            ->where('hotel_id', $receptionist->hotel_id)
            ->whereIn('status', ['checked_in', 'confirmed'])
            ->latest()
            ->get();

        return response()->json([
            'data' => $bookings->map(fn($b) => [
                'id'            => $b->id,
                'guest_name'    => $b->guest_name,
                'guest_email'   => $b->guest_email,
                'guest_phone'   => $b->guest_phone,
                'room'          => [
                    'room_number' => $b->room->room_number ?? '-',
                    'room_type'   => $b->room->room_type ?? '-',
                ],
                'check_in_date' => $b->check_in_date,
                'check_out_date' => $b->check_out_date,
                'status'        => $b->status,
                'total_price'   => $b->total_price,
            ]),
        ]);
    }

    /**
     * Ambil semua booking yang sudah check-out
     */
    public function checkedOut(Request $request)
    {
        $receptionist = $request->user()->receptionist;

        if (!$receptionist) {
            return response()->json(['message' => 'Anda bukan receptionist'], 403);
        }

        $bookings = Booking::with('room')
            ->where('hotel_id', $receptionist->hotel_id)
            ->where('status', 'checked_out')
            ->latest()
            ->get();

        return response()->json([
            'data' => $bookings->map(fn($b) => [
                'id'            => $b->id,
                'guest_name'    => $b->guest_name,
                'guest_email'   => $b->guest_email,
                'guest_phone'   => $b->guest_phone,
                'room'          => [
                    'room_number' => $b->room->room_number ?? '-',
                    'room_type'   => $b->room->room_type ?? '-',
                ],
                'check_in_date' => $b->check_in_date,
                'check_out_date' => $b->check_out_date,
                'status'        => $b->status,
                'total_price'   => $b->total_price,
            ]),
        ]);
    }
}
