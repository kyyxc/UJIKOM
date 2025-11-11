<?php

namespace App\Http\Controllers\Api\Hotel;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::with(['amenities', 'images', 'rooms'])
            ->whereHas('owner', function ($q) {
                $q->where('registration_status', 'approved');
            });

        // Search by name, city, or address
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('state_province', 'like', '%' . $search . '%')
                    ->orWhere('country', 'like', '%' . $search . '%');
            });
        }

        // Filter by check-in and check-out dates (availability)
        if ($request->has('check_in') && $request->has('check_out')) {
            $checkIn = $request->check_in;
            $checkOut = $request->check_out;

            // Filter hotels that have available rooms during the date range
            $query->whereHas('rooms', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', 'available')
                    ->whereDoesntHave('bookings', function ($bookingQuery) use ($checkIn, $checkOut) {
                        $bookingQuery->where(function ($q) use ($checkIn, $checkOut) {
                            $q->whereBetween('check_in_date', [$checkIn, $checkOut])
                                ->orWhereBetween('check_out_date', [$checkIn, $checkOut])
                                ->orWhere(function ($q) use ($checkIn, $checkOut) {
                                    $q->where('check_in_date', '<=', $checkIn)
                                        ->where('check_out_date', '>=', $checkOut);
                                });
                        })->whereIn('status', ['pending', 'confirmed', 'checked_in']);
                    });
            });
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $hotels = $query->paginate($perPage);

        // Add additional information to each hotel
        $hotels->getCollection()->transform(function ($hotel) {
            $hotel->min_price = $hotel->rooms->min('price_per_night');
            $hotel->max_price = $hotel->rooms->max('price_per_night');
            $hotel->available_rooms_count = $hotel->rooms->where('status', 'available')->count();
            $hotel->total_rooms_count = $hotel->rooms->count();
            return $hotel;
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Get all hotel success',
            'data' => $hotels,
            'filters' => [
                'search' => $request->search,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'guests' => $request->guests,
                'room_type' => $request->room_type,
                'rating' => $request->rating,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'city' => $request->city,
                'country' => $request->country,
                'facilities' => $request->facilities,
            ],
        ], 200);
    }


    public function show(Hotel $hotel)
    {
        // Check if hotel owner is approved
        if (!$hotel->owner || $hotel->owner->registration_status !== 'approved') {
            return response()->json([
                'status' => 'error',
                'message' => 'Hotel not available or owner not approved',
            ], 404);
        }

        $hotel->load([
            'amenities' => fn($q) => $q->where('type', 'hotel'),
            'images',
            'rooms.amenities',
            'rooms.images',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Get detail hotel success',
            'data' => $hotel,
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state_province' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'star_rating' => 'required|integer|min:1|max:5',
            'check_in_time' => 'date_format:H:i:s',
            'check_out_time' => 'date_format:H:i:s',
            'cancellation_policy' => 'nullable|string',
            'is_active' => 'required|boolean',
            'amenities' => 'array',
            'amenities.*' => 'integer|exists:amenities,id',
            'images' => 'array',
            'images.*' => 'required|image|mimes:png,jpg,jpeg,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid body',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();

        $hotel = Hotel::create($data);

        if (!empty($data['amenities'])) {
            $hotel->amenities()->sync($data['amenities']);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('hotels', 'public');
                $hotel->images()->create([
                    'image_url' => $path,
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Create hotel successfully',
            'data' => $hotel,
        ]);
    }

    public function update(Request $request, Hotel $hotel)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'address' => 'sometimes|required|string',
            'city' => 'sometimes|required|string|max:255',
            'state_province' => 'sometimes|required|string|max:100',
            'country' => 'sometimes|required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'star_rating' => 'sometimes|required|integer|min:1|max:5',
            'check_in_time' => 'nullable|date_format:H:i:s',
            'check_out_time' => 'nullable|date_format:H:i:s',
            'cancellation_policy' => 'nullable|string',
            'is_active' => 'boolean',
            'amenities' => 'array',
            'amenities.*' => 'integer|exists:amenities,id',
            'images' => 'array',
            'images.*' => 'required|file|mimes:png,jpg,jpeg,webp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid body',
                'errors' => $validator->errors(),
            ], 400);
        }

        $data = $validator->validated();

        $hotel->update($data);

        if (!empty($data['amenities'])) {
            $hotel->amenities()->sync($data['amenities']);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('hotels', 'public');
                $hotel->images()->create([
                    'image_url' => $path,
                ]);
            }
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Update hotel successfully',
            'data' => $hotel->load('amenities', 'images'),
        ]);
    }

    public function destroy(Request $request, Hotel $hotel)
    {
        $hotel->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Hotel deleted successfully',
        ]);
    }

    public function getRooms($id)
    {
        $hotel = Hotel::find($id);

        if (!$hotel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hotel not found',
            ], 404);
        }

        $rooms = $hotel->rooms()
            ->with(['images', 'amenities'])
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Rooms retrieved successfully',
            'data' => $rooms,
        ], 200);
    }
}
