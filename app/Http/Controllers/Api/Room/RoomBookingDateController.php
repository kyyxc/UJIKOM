<?php

namespace App\Http\Controllers\Api\Room;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomBookingDateController extends Controller
{
    public function getBookedDates($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Room not found'
            ], 404);
        }

        $bookedDates = $room->bookings()
            ->whereIn('status', ['confirmed', 'checked_in'])
            ->get(['check_in_date', 'check_out_date'])
            ->map(function ($booking) {
                return [
                    'check_in_date' => $booking->check_in_date,
                    'check_out_date' => $booking->check_out_date,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $bookedDates
        ], 200);
    }
}
