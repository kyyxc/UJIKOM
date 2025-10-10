<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminHotelController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::with(['amenities', 'images']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('city', 'like', '%' . $request->search . '%');
        }

        // Rating
        if ($request->has('rating') && $request->rating) {
            $query->where('star_rating', '>=', (int) $request->rating);
        }

        // Price (contoh kalau ada relasi rooms)
        if ($request->has('price') && $request->price) {
            if ($request->price === 'low') {
                $query->orderBy('star_rating', 'asc');
            } elseif ($request->price === 'high') {
                $query->orderBy('star_rating', 'desc');
            }
        }

        // Facilities
        if ($request->has('facilities') && $request->facilities) {
            $facilities = explode(',', $request->facilities);
            $query->whereHas('amenities', function ($q) use ($facilities) {
                $q->whereIn('name', $facilities);
            });
        }

        $hotels = $query->paginate(10);

        return response()->json([
            'status' => 'success',
            'message' => 'Get all hotel success',
            'data' => $hotels,
        ], 200);
    }


    public function show(Hotel $hotel)
    {
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
}
