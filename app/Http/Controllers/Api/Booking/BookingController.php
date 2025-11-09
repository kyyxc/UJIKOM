<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'hotel', 'room.images'])
            ->where('status', '!=', 'booked')->latest(); // ❌ exclude booked

        // Optional search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('guest', function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        }

        // Optional filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Optional filter by hotel
        if ($request->has('hotel_id') && !empty($request->hotel_id)) {
            $query->where('hotel_id', $request->hotel_id);
        }

        $bookings = $query->orderBy('check_in_date', 'desc')->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking list retrieved successfully',
            'data' => $bookings,
        ]);
    }

    public function show($id)
    {
        $booking = Booking::with([
            'hotel',
            'room.images',
            'room.amenities',
            'guest',
            'payment'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $booking,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id'        => 'required|exists:rooms,id',
            'hotel_id'       => 'required|exists:hotels,id',
            'check_in_date'  => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'total_price'    => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // Hitung total_price jika tidak diberikan
        $totalPrice = $request->total_price;
        if (!$totalPrice) {
            $room = \App\Models\Room::findOrFail($request->room_id);
            $checkIn = new \DateTime($request->check_in_date);
            $checkOut = new \DateTime($request->check_out_date);
            $nights = $checkOut->diff($checkIn)->days;
            $totalPrice = $room->price_per_night * $nights;
        }

        $booking = Booking::create([
            'user_id'        => $user->id,
            'room_id'        => $request->room_id,
            'hotel_id'       => $request->hotel_id,
            'guest_name' => trim($user->first_name . ' ' . ($user->last_name ?? '')),
            'guest_email'    => $user->email,
            'guest_phone'    => $user->phone ?? null,
            'check_in_date'  => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'status'         => 'pending',
            'source'         => 'online',
            'total_price'    => $totalPrice,
        ]);

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(uniqid());

        $invoice = Invoice::create([
            'booking_id'     => $booking->id,
            'invoice_number' => $invoiceNumber,
            'amount'         => $booking->total_price,
            'invoice_date'   => now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Booking created successfully',
            'data'    => $booking,
        ], 201);
    }

    public function destroy(Booking $booking, Request $request)
    {
        // Cek apakah booking sudah dibatalkan / selesai
        if ($booking->status === 'cancelled' || $booking->status === 'completed') {
            return response()->json([
                'message' => 'Booking tidak bisa dibatalkan.',
                'data' => $booking
            ], 400);
        }

        // Update status booking jadi cancelled
        $booking->update([
            'status' => 'cancelled',
        ]);

        // Kalau ada payment → update status payment juga
        if ($booking->payment) {
            $booking->payment->update([
                'status' => 'cancelled',
            ]);
        }

        // Kalau ada room → ubah status room jadi "available" lagi
        if ($booking->room) {
            $booking->room->update([
                'status' => 'available',
            ]);
        }

        return response()->json([
            'message' => 'Booking berhasil dibatalkan.',
            'data' => $booking->load(['room', 'hotel', 'guest', 'payment'])
        ]);
    }
}
