<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceptionistRoomController extends Controller
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

        // Ambil semua kamar di hotel tempat receptionist bekerja
        $rooms = Room::where('hotel_id', $hotelId)
            ->select('id', 'room_number', 'room_type', 'capacity', 'price_per_night', 'status')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Room list retrieved successfully',
            'rooms' => $rooms,
        ], 200);
    }

    public function show($id)
    {
        $user = Auth::user();

        if (!$user->receptionist) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized: Only receptionist can access this resource.',
            ], 403);
        }

        $hotelId = $user->receptionist->hotel_id;

        $room = Room::where('hotel_id', $hotelId)->find($id);

        if (!$room) {
            return response()->json([
                'status' => 'error',
                'message' => 'Room not found or not accessible',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'room' => $room,
        ], 200);
    }
}
