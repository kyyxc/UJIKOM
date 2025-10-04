<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
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

    // BookingController.php
    public function booking(Request $request)
    {
        $validated = $request->validate([
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email',
            'guest_phone' => 'nullable|string',
            'room_id' => 'required|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_price' => 'required|numeric',
            'source' => 'required|in:online,offline',
            'status' => 'required|in:pending,confirmed,booked,checked_in,checked_out,cancelled',
        ]);

        // Get receptionist ID from authenticated user
        $receptionistId = $request->user()->receptionist->id;
        $hotelId = $request->user()->receptionist->hotel_id;

        $booking = Booking::create([
            ...$validated,
            'receptionist_id' => $receptionistId,
            'hotel_id' => $hotelId,
            'user_id' => null, // Offline booking
        ]);

        // Update room status to occupied
        Room::where('id', $validated['room_id'])->update(['status' => 'occupied']);

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking
        ], 201);
    }

    public function payment(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method' => 'required|string',
            'amount' => 'required|numeric',
            'status' => 'required|in:pending,paid,failed,cancelled,expired,refunded',
            'midtrans_order_id' => 'required|string|unique:payments,midtrans_order_id',
            'transaction_date' => 'nullable|date',
        ]);

        $payment = Payment::create($validated);

        return response()->json([
            'message' => 'Payment processed successfully',
            'payment' => $payment
        ], 201);
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
