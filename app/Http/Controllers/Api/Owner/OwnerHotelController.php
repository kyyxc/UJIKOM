<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Hotel;
use Illuminate\Http\Request;

class OwnerHotelController extends Controller
{
    /**
     * Get owner's hotel detail with registration status
     * GET /api/owner/hotel
     */
    public function getHotelDetail(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get owner data with hotel relation
            $owner = Owner::with([
                'hotel.amenities',
                'hotel.images',
                'hotel.rooms' => function ($query) {
                    $query->where('status', 'available');
                }
            ])->where('user_id', $user->id)->first();

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner data not found. Please complete registration first.'
                ], 404);
            }

            // Check if hotel exists
            if (!$owner->hotel) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hotel not yet created. Please complete registration.',
                    'data' => [
                        'owner_id' => $owner->id,
                        'registration_status' => $owner->registration_status,
                        'current_step' => $this->getCurrentStep($owner->registration_status),
                        'hotel' => null
                    ]
                ], 200);
            }

            $hotel = $owner->hotel;

            // Prepare response data
            $data = [
                'owner_id' => $owner->id,
                'registration_status' => $owner->registration_status,
                'registration_info' => [
                    'status' => $owner->registration_status,
                    'status_label' => $this->getStatusLabel($owner->registration_status),
                    'current_step' => $this->getCurrentStep($owner->registration_status),
                    'is_approved' => $owner->registration_status === 'approved',
                    'is_pending' => $owner->registration_status === 'completed',
                    'is_rejected' => $owner->registration_status === 'rejected',
                    'submitted_at' => $owner->submitted_at,
                    'approved_at' => $owner->approved_at,
                    'rejection_reason' => $owner->rejection_reason,
                ],
                'hotel' => [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'description' => $hotel->description,
                    'address' => $hotel->address,
                    'city' => $hotel->city,
                    'state_province' => $hotel->state_province,
                    'country' => $hotel->country,
                    'latitude' => $hotel->latitude,
                    'longitude' => $hotel->longitude,
                    'email' => $hotel->email,
                    'phone' => $hotel->phone,
                    'website' => $hotel->website,
                    'star_rating' => $hotel->star_rating,
                    'check_in_time' => $hotel->check_in_time,
                    'check_out_time' => $hotel->check_out_time,
                    'cancellation_policy' => $hotel->cancellation_policy,
                    'is_active' => $hotel->is_active,
                    'amenities_count' => $hotel->amenities->count(),
                    'images_count' => $hotel->images->count(),
                    'rooms_count' => $hotel->rooms->count(),
                    'amenities' => $hotel->amenities->map(function ($amenity) {
                        return [
                            'id' => $amenity->id,
                            'name' => $amenity->name,
                            'type' => $amenity->type,
                        ];
                    }),
                    'images' => $hotel->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => asset('storage/' . $image->image_url),
                        ];
                    }),
                    'primary_image' => $hotel->images->first() 
                        ? asset('storage/' . $hotel->images->first()->image_url)
                        : null,
                    'created_at' => $hotel->created_at,
                    'updated_at' => $hotel->updated_at,
                ],
                'banking_info' => [
                    'bank_name' => $owner->bank_name,
                    'account_number' => $owner->account_number,
                    'account_holder_name' => $owner->account_holder_name,
                ],
                'completion_percentage' => $this->calculateCompletionPercentage($owner, $hotel),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get hotel detail',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration status only
     * GET /api/owner/hotel/registration-status
     */
    public function getRegistrationStatus(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found'
                ], 404);
            }

            $data = [
                'owner_id' => $owner->id,
                'registration_status' => $owner->registration_status,
                'status_label' => $this->getStatusLabel($owner->registration_status),
                'current_step' => $this->getCurrentStep($owner->registration_status),
                'is_approved' => $owner->registration_status === 'approved',
                'is_pending' => $owner->registration_status === 'completed',
                'is_rejected' => $owner->registration_status === 'rejected',
                'is_incomplete' => in_array($owner->registration_status, ['pending', 'step_1', 'step_2', 'step_3', 'step_4']),
                'can_manage_hotel' => $owner->registration_status === 'approved',
                'submitted_at' => $owner->submitted_at,
                'approved_at' => $owner->approved_at,
                'rejection_reason' => $owner->rejection_reason,
                'next_action' => $this->getNextAction($owner->registration_status),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get registration status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update hotel basic information
     * PUT /api/owner/hotel
     */
    public function updateHotel(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::with('hotel')->where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Only approved owners can update hotel
            if ($owner->registration_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved owners can update hotel information'
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'address' => 'sometimes|string',
                'city' => 'sometimes|string|max:255',
                'state_province' => 'sometimes|string|max:100',
                'country' => 'sometimes|string|max:100',
                'latitude' => 'sometimes|numeric',
                'longitude' => 'sometimes|numeric',
                'email' => 'sometimes|email',
                'phone' => 'sometimes|string|max:20',
                'website' => 'sometimes|url',
                'star_rating' => 'sometimes|integer|min:1|max:5',
                'check_in_time' => 'sometimes|date_format:H:i',
                'check_out_time' => 'sometimes|date_format:H:i',
                'cancellation_policy' => 'sometimes|string',
                'amenities' => 'sometimes|array',
                'amenities.*' => 'exists:amenities,id',
                'images' => 'sometimes|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            ]);

            // Update basic hotel info
            $hotel = $owner->hotel;
            $hotel->update($validated);

            // Update amenities if provided
            if ($request->has('amenities')) {
                $hotel->amenities()->sync($request->amenities);
            }

            // Handle images if provided
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('hotels/images', 'public');
                    $hotel->images()->create([
                        'image_url' => $path,
                    ]);
                }
            }

            // Reload hotel with relations
            $hotel->load('amenities', 'images');

            // Reload hotel with relations
            $hotel->load('amenities', 'images');

            return response()->json([
                'success' => true,
                'message' => 'Hotel updated successfully',
                'data' => [
                    'hotel' => $hotel
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update hotel',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hotel statistics
     * GET /api/owner/hotel/statistics
     */
    public function getHotelStatistics(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::with('hotel.rooms', 'hotel.images', 'hotel.amenities')
                ->where('user_id', $user->id)
                ->first();

            if (!$owner || !$owner->hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            $hotel = $owner->hotel;

            $stats = [
                'total_rooms' => $hotel->rooms->count(),
                'available_rooms' => $hotel->rooms->where('status', 'available')->count(),
                'unavailable_rooms' => $hotel->rooms->where('status', '!=', 'available')->count(),
                'total_amenities' => $hotel->amenities->count(),
                'total_images' => $hotel->images->count(),
                'hotel_active' => $hotel->is_active,
                'registration_status' => $owner->registration_status,
                'can_receive_bookings' => $hotel->is_active && $owner->registration_status === 'approved',
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get hotel statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle hotel active status
     * POST /api/owner/hotel/toggle-status
     */
    public function toggleHotelStatus(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::with('hotel')->where('user_id', $user->id)->first();

            if (!$owner || !$owner->hotel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hotel not found'
                ], 404);
            }

            // Only approved owners can toggle status
            if ($owner->registration_status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved owners can toggle hotel status'
                ], 403);
            }

            $hotel = $owner->hotel;
            $hotel->is_active = !$hotel->is_active;
            $hotel->save();

            return response()->json([
                'success' => true,
                'message' => 'Hotel status updated successfully',
                'data' => [
                    'hotel_id' => $hotel->id,
                    'is_active' => $hotel->is_active,
                    'status_label' => $hotel->is_active ? 'Active' : 'Inactive'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle hotel status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Get current step number
     */
    private function getCurrentStep($status)
    {
        $stepMap = [
            'pending' => 1,
            'step_1' => 1,
            'step_2' => 2,
            'step_3' => 3,
            'step_4' => 4,
            'completed' => 5,
            'approved' => 5,
            'rejected' => null,
        ];

        return $stepMap[$status] ?? 1;
    }

    /**
     * Helper: Get status label in Indonesian
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Belum Dimulai',
            'step_1' => 'Step 1 - Akun Terdaftar',
            'step_2' => 'Step 2 - Data Hotel Tersimpan',
            'step_3' => 'Step 3 - Fasilitas & Foto Tersimpan',
            'step_4' => 'Step 4 - Data Rekening Tersimpan',
            'completed' => 'Menunggu Persetujuan Admin',
            'approved' => 'Disetujui - Hotel Aktif',
            'rejected' => 'Ditolak',
        ];

        return $labels[$status] ?? 'Unknown Status';
    }

    /**
     * Helper: Get next action recommendation
     */
    private function getNextAction($status)
    {
        $actions = [
            'pending' => 'Mulai registrasi dengan membuat akun',
            'step_1' => 'Lanjutkan ke Step 2 - Input data hotel',
            'step_2' => 'Lanjutkan ke Step 3 - Upload fasilitas & foto',
            'step_3' => 'Lanjutkan ke Step 4 - Input data rekening & dokumen',
            'step_4' => 'Lanjutkan ke Step 5 - Konfirmasi & submit',
            'completed' => 'Tunggu persetujuan dari admin',
            'approved' => 'Anda dapat mengelola hotel',
            'rejected' => 'Perbaiki data dan ajukan ulang',
        ];

        return $actions[$status] ?? null;
    }

    /**
     * Helper: Calculate registration completion percentage
     */
    private function calculateCompletionPercentage($owner, $hotel)
    {
        $percentage = 0;

        // Step 1: User & Owner created (20%)
        if ($owner) {
            $percentage += 20;
        }

        // Step 2: Hotel basic info (20%)
        if ($hotel && $hotel->name) {
            $percentage += 20;
        }

        // Step 3: Amenities & Images (20%)
        if ($hotel && $hotel->amenities->count() > 0) {
            $percentage += 10;
        }
        if ($hotel && $hotel->images->count() > 0) {
            $percentage += 10;
        }

        // Step 4: Banking & Documents (20%)
        if ($owner->bank_name && $owner->business_license_file) {
            $percentage += 20;
        }

        // Step 5: Submitted (20%)
        if ($owner->registration_status === 'completed' || $owner->registration_status === 'approved') {
            $percentage += 20;
        }

        return $percentage;
    }
}
