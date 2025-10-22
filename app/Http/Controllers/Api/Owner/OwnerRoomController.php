<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Room;
use App\Models\RoomImage;
use App\Models\RoomAmenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OwnerRoomController extends Controller
{
    /**
     * Get all rooms for owner's hotel
     * GET /api/owner/rooms
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found. Please complete registration first.'
                ], 404);
            }

            // Only approved owners can manage rooms
            if ($owner->registration_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved owners can manage rooms'
                ], 403);
            }

            $query = Room::with(['images', 'amenities'])
                ->where('hotel_id', $owner->hotel_id);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by room type
            if ($request->has('room_type')) {
                $query->where('room_type', $request->room_type);
            }

            // Search by room number
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('room_number', 'ILIKE', "%{$search}%")
                      ->orWhere('description', 'ILIKE', "%{$search}%");
                });
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'room_number');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $rooms = $query->paginate($perPage);

            // Transform data
            $rooms->getCollection()->transform(function ($room) {
                return [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'room_type' => $room->room_type,
                    'description' => $room->description,
                    'capacity' => $room->capacity,
                    'price_per_night' => $room->price_per_night,
                    'status' => $room->status,
                    'status_label' => $this->getStatusLabel($room->status),
                    'images_count' => $room->images->count(),
                    'amenities_count' => $room->amenities->count(),
                    'primary_image' => $room->images->first() 
                        ? asset('storage/' . $room->images->first()->image_url)
                        : null,
                    'created_at' => $room->created_at,
                    'updated_at' => $room->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $rooms
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get rooms',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room detail
     * GET /api/owner/rooms/{id}
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Get room and verify it belongs to owner's hotel
            $room = Room::with(['images', 'amenities'])
                ->where('hotel_id', $owner->hotel_id)
                ->where('id', $id)
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found or does not belong to your hotel'
                ], 404);
            }

            $data = [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'room_type' => $room->room_type,
                'description' => $room->description,
                'capacity' => $room->capacity,
                'price_per_night' => $room->price_per_night,
                'status' => $room->status,
                'status_label' => $this->getStatusLabel($room->status),
                'images' => $room->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'image_url' => asset('storage/' . $image->image_url),
                        'created_at' => $image->created_at,
                    ];
                }),
                'amenities' => $room->amenities->map(function ($amenity) {
                    return [
                        'id' => $amenity->id,
                        'name' => $amenity->name,
                        'type' => $amenity->type,
                    ];
                }),
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get room detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create new room
     * POST /api/owner/rooms
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Only approved owners can create rooms
            if ($owner->registration_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved owners can create rooms'
                ], 403);
            }

            $validated = $request->validate([
                'room_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('rooms')->where(function ($query) use ($owner) {
                        return $query->where('hotel_id', $owner->hotel_id);
                    })
                ],
                'room_type' => 'required|string|max:100',
                'description' => 'nullable|string',
                'capacity' => 'required|integer|min:1|max:10',
                'price_per_night' => 'required|numeric|min:0',
                'status' => 'required|in:available,occupied,maintenance',
                'amenity_ids' => 'nullable|array',
                'amenity_ids.*' => 'exists:amenities,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            DB::beginTransaction();

            // Create room
            $room = Room::create([
                'hotel_id' => $owner->hotel_id,
                'room_number' => $validated['room_number'],
                'room_type' => $validated['room_type'],
                'description' => $validated['description'],
                'capacity' => $validated['capacity'],
                'price_per_night' => $validated['price_per_night'],
                'status' => $validated['status'],
            ]);

            // Add amenities using firstOrCreate to prevent duplicates
            if (!empty($validated['amenity_ids'])) {
                foreach ($validated['amenity_ids'] as $amenityId) {
                    RoomAmenity::firstOrCreate([
                        'room_id' => $room->id,
                        'amenity_id' => $amenityId,
                    ]);
                }
            }

            // Upload images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('room-images', 'public');
                    
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_url' => $path,
                    ]);
                }
            }

            DB::commit();

            // Load relations
            $room->load(['images', 'amenities']);

            return response()->json([
                'success' => true,
                'message' => 'Room created successfully',
                'data' => $room
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update room
     * PUT /api/owner/rooms/{id}
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Only approved owners can update rooms
            if ($owner->registration_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved owners can update rooms'
                ], 403);
            }

            // Get room and verify ownership
            $room = Room::where('hotel_id', $owner->hotel_id)
                ->where('id', $id)
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found or does not belong to your hotel'
                ], 404);
            }

            $validated = $request->validate([
                'room_number' => [
                    'sometimes',
                    'string',
                    'max:50',
                    Rule::unique('rooms')->where(function ($query) use ($owner) {
                        return $query->where('hotel_id', $owner->hotel_id);
                    })->ignore($room->id)
                ],
                'room_type' => 'sometimes|string|max:100',
                'description' => 'sometimes|string',
                'capacity' => 'sometimes|integer|min:1|max:10',
                'price_per_night' => 'sometimes|numeric|min:0',
                'status' => 'sometimes|in:available,occupied,maintenance',
                'amenity_ids' => 'nullable|array',
                'amenity_ids.*' => 'exists:amenities,id',
                'images' => 'nullable|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
                'delete_image_ids' => 'nullable|array',
                'delete_image_ids.*' => 'integer|exists:room_images,id',
            ]);

            DB::beginTransaction();

            // Update basic room info
            $room->update(array_intersect_key($validated, array_flip([
                'room_number', 'room_type', 'description', 'capacity', 'price_per_night', 'status'
            ])));

            // Update amenities if provided - using sync pattern
            if ($request->has('amenity_ids')) {
                // Get existing amenity IDs
                $existingAmenityIds = RoomAmenity::where('room_id', $room->id)
                    ->pluck('amenity_id')
                    ->toArray();
                
                $newAmenityIds = $validated['amenity_ids'] ?? [];
                
                // Find amenities to delete (yang ada di existing tapi tidak ada di new)
                $amenityIdsToDelete = array_diff($existingAmenityIds, $newAmenityIds);
                if (!empty($amenityIdsToDelete)) {
                    RoomAmenity::where('room_id', $room->id)
                        ->whereIn('amenity_id', $amenityIdsToDelete)
                        ->delete();
                }
                
                // Add new amenities using firstOrCreate (yang ada di new tapi tidak ada di existing)
                foreach ($newAmenityIds as $amenityId) {
                    RoomAmenity::firstOrCreate([
                        'room_id' => $room->id,
                        'amenity_id' => $amenityId,
                    ]);
                }
            }

            // Delete specified images
            if (!empty($validated['delete_image_ids'])) {
                $imagesToDelete = RoomImage::where('room_id', $room->id)
                    ->whereIn('id', $validated['delete_image_ids'])
                    ->get();

                foreach ($imagesToDelete as $image) {
                    if (Storage::disk('public')->exists($image->image_url)) {
                        Storage::disk('public')->delete($image->image_url);
                    }
                    $image->delete();
                }
            }

            // Upload new images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('room-images', 'public');
                    
                    RoomImage::create([
                        'room_id' => $room->id,
                        'image_url' => $path,
                    ]);
                }
            }

            DB::commit();

            $room->load(['images', 'amenities']);

            return response()->json([
                'success' => true,
                'message' => 'Room updated successfully',
                'data' => $room
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete room
     * DELETE /api/owner/rooms/{id}
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Only approved owners can delete rooms
            if ($owner->registration_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved owners can delete rooms'
                ], 403);
            }

            // Get room and verify ownership
            $room = Room::with('images')
                ->where('hotel_id', $owner->hotel_id)
                ->where('id', $id)
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found or does not belong to your hotel'
                ], 404);
            }

            DB::beginTransaction();

            // Delete room images from storage
            foreach ($room->images as $image) {
                if (Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }
                $image->delete();
            }

            // Delete room amenities
            RoomAmenity::where('room_id', $room->id)->delete();

            // Delete room
            $room->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Room deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete room',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update room status
     * PATCH /api/owner/rooms/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Get room and verify ownership
            $room = Room::where('hotel_id', $owner->hotel_id)
                ->where('id', $id)
                ->first();

            if (!$room) {
                return response()->json([
                    'success' => false,
                    'message' => 'Room not found or does not belong to your hotel'
                ], 404);
            }

            $validated = $request->validate([
                'status' => 'required|in:available,occupied,maintenance',
            ]);

            $room->update(['status' => $validated['status']]);

            return response()->json([
                'success' => true,
                'message' => 'Room status updated successfully',
                'data' => [
                    'id' => $room->id,
                    'room_number' => $room->room_number,
                    'status' => $room->status,
                    'status_label' => $this->getStatusLabel($room->status),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update room status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room statistics for owner's hotel
     * GET /api/owner/rooms/statistics
     */
    public function statistics(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            $query = Room::where('hotel_id', $owner->hotel_id);

            $stats = [
                'total_rooms' => $query->count(),
                'available_rooms' => (clone $query)->where('status', 'available')->count(),
                'occupied_rooms' => (clone $query)->where('status', 'occupied')->count(),
                'maintenance_rooms' => (clone $query)->where('status', 'maintenance')->count(),
                'by_type' => (clone $query)->select('room_type', DB::raw('count(*) as count'))
                    ->groupBy('room_type')
                    ->get()
                    ->mapWithKeys(function ($item) {
                        return [$item->room_type => $item->count];
                    }),
                'average_price' => (clone $query)->avg('price_per_night'),
                'min_price' => (clone $query)->min('price_per_night'),
                'max_price' => (clone $query)->max('price_per_night'),
                'total_capacity' => (clone $query)->sum('capacity'),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Get status label in Indonesian
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'available' => 'Tersedia',
            'occupied' => 'Terisi',
            'maintenance' => 'Dalam Perbaikan',
        ];

        return $labels[$status] ?? $status;
    }
}
