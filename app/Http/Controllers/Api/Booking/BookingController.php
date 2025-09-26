<?php

namespace App\Http\Controllers\Api\Booking;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'hotel', 'room']);

        // Optional search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
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

    public function show(Booking $booking)
    {
        $booking->load(['room', 'hotel']);
        // $booking = Booking::with(['room', 'hotel'])->find($id);

        return response()->json([
            'id'           => $booking->id,
            'guest_name'   => $booking->guest_name,
            'room'         => $booking->room->room_number . ' - ' . ucfirst($booking->room->room_type),
            'check_in_date' => $booking->check_in_date,
            'check_out_date' => $booking->check_out_date,
            'status'       => $booking->status,
        ]);
    }
}
