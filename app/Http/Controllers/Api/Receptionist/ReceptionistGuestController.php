<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptionistGuestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Pastikan user adalah receptionist
        if (!$user->receptionist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Only receptionist can access this resource.',
            ], 403);
        }

        $hotelId = $user->receptionist->hotel_id;

        // Ambil bookings untuk hotel ini
        $bookings = Booking::with(['room'])
            ->where('hotel_id', $hotelId)
            ->whereIn('status', ['checked_in', 'checked_out', 'booked'])
            ->get();

        $guests = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'name' => $booking->guest_name ?? ($booking->user?->first_name . ' ' . $booking->user?->last_name),
                'email' => $booking->guest_email ?? $booking->user?->email,
                'phone' => $booking->guest_phone ?? $booking->user?->phone,
                'roomNumber' => $booking->room?->room_number ?? '-',
                'roomType' => $booking->room?->type ?? '-',
                'checkInDate' => $booking->check_in_date,
                'checkOutDate' => $booking->check_out_date,
                'status' => $booking->status,
                'totalGuests' => $booking->total_guests ?? 1, // kalau belum ada kolom, default 1
            ];
        });

        return response()->json($guests);
    }
}
