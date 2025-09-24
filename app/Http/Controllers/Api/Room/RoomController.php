<?php

namespace App\Http\Controllers\Api\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends Controller
{
    public function store(RoomRequest $request)
    {
        $room = Room::create($request->only([
            'hotel_id',
            'room_number',
            'room_type',
            'description',
            'capacity',
            'price_per_night',
            'status'
        ]));

        if ($request->filled('amenities')) {
            $room->amenities()->sync($request->amenities);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('room_images', 'public');
                $room->images()->create([
                    'image_url' => $path,
                ]);
            }
        }

        return response()->json($room->load(['amenities', 'images']), 201);
    }

    public function show(Room $room)
    {
        return response()->json($room->load(['amenities', 'images']));
    }

    public function update(RoomRequest $request, Room $room)
    {
        $room->update($request->only([
            'hotel_id',
            'room_number',
            'room_type',
            'description',
            'capacity',
            'price_per_night',
            'status'
        ]));

        if ($request->filled('amenities')) {
            $room->amenities()->sync($request->amenities);
        }

        if ($request->hasFile('images')) {
            foreach ($room->images as $img) {
                Storage::disk('public')->delete($img->url);
                $img->delete();
            }

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('room_images', 'public');
                $room->images()->create([
                    'image_url' => $path,
                ]);
            }
        }

        return response()->json($room->load(['amenities', 'images']));
    }

    public function destroy(Room $room)
    {
        foreach ($room->images as $img) {
            Storage::disk('public')->delete($img->url);
            $img->delete();
        }
        $room->amenities()->detach();
        $room->delete();

        return response()->json(['message' => 'Room deleted successfully']);
    }

    public function index()
    {
        return response()->json(
            Room::with(['amenities', 'images'])->paginate(10)
        );
    }
}
