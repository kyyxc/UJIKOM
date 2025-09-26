<?php

namespace App\Http\Controllers\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReceptionistBookingController extends Controller
{
    public function index(Request $request)
    {
        $receptionist = $request->user()->receptionist;

        if (!$receptionist) {
            return response()->json(['message' => 'Anda bukan receptionist'], 403);
        }

        $bookings = Booking::with('room')
            ->where('hotel_id', $receptionist->hotel_id)
            ->where('source', 'online') // hanya booking online
            ->latest()
            ->get();

        return response()->json($bookings->map(function ($b) {
            return [
                'id'            => $b->id,
                'guest_name'    => $b->guest_name,
                'room'          => $b->room->room_number . ' - ' . ucfirst($b->room->room_type),
                'check_in_date' => $b->check_in_date,
                'check_out_date' => $b->check_out_date,
                'status'        => $b->status,
            ];
        }));
    }

    public function store(Request $request)
    {
        // Validasi menggunakan Validator facade
        $validator = Validator::make($request->all(), [
            'room_id'       => 'required|exists:rooms,id',
            'guest_name'    => 'required|string|max:255',
            'guest_email'   => 'nullable|email',
            'guest_phone'   => 'nullable|string|max:20',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
        ]);

        // Jika validasi gagal, return JSON error
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        $room = Room::findOrFail($request->room_id);

        // Hitung total harga
        $days = (new \DateTime($request->check_in_date))
            ->diff(new \DateTime($request->check_out_date))
            ->days;

        $totalPrice = $room->price_per_night * $days;

        // Buat booking langsung
        $booking = Booking::create([
            'user_id'         => null, // karena offline
            'receptionist_id' => Auth::user()->receptionist->id, // receptionist yg login
            'room_id'         => $room->id,
            'hotel_id'        => Auth::user()->receptionist->hotel_id,
            'guest_name'      => $request->guest_name,
            'guest_email'     => $request->guest_email,
            'guest_phone'     => $request->guest_phone,
            'check_in_date'   => $request->check_in_date,
            'check_out_date'  => $request->check_out_date,
            'status'          => 'booked',   // langsung booked
            'source'          => 'offline',  // tandai offline booking
            'total_price'     => $totalPrice,
        ]);

        // Ubah status room jadi occupied
        $room->update(['status' => 'occupied']);

        return response()->json([
            'message' => 'Booking berhasil dibuat oleh receptionist',
            'booking' => $booking,
        ]);
    }

    public function checkIn($id)
    {
        $booking = Booking::findOrFail($id);

        // Validasi status booking
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json([
                'message' => 'Booking tidak bisa di-check-in',
            ], 400);
        }

        // Update status booking
        $booking->status = 'checked_in';
        $booking->save();

        // Update status room jadi occupied
        $booking->room->update(['status' => 'occupied']);

        return response()->json([
            'message' => 'Check-In berhasil',
            'booking' => $booking,
        ]);
    }

    public function checkOut(Request $request, Booking $booking)
    {
        if ($booking->status !== 'checked_in') {
            return response()->json([
                'message' => 'Booking ini tidak bisa di-check-out!'
            ], 400);
        }

        $extraCharge = $request->input('extra_charge', 0);

        // Update status booking dan total harga
        $booking->status = 'checked_out';
        $booking->total_price = ($booking->total_price ?? 0) + $extraCharge;
        $booking->save();

        // Ubah status kamar menjadi available
        if ($booking->room) {
            $booking->room->status = 'available';
            $booking->room->save();
        }

        return response()->json([
            'message' => 'Check-Out berhasil!',
            'booking' => $booking
        ]);
    }
}
