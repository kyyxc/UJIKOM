<?php

namespace App\Http\Controllers\Api\Room;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return response()->json(
            Room::with(['amenities', 'images'])->paginate(10)
        );
    }

    public function show(Room $room)
    {
        return response()->json($room->load(['amenities', 'images', 'hotel']));
    }
}
