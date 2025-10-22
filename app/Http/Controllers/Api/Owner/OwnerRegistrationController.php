<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\User;
use App\Models\Hotel;
use App\Models\HotelAmenity;
use App\Models\HotelImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OwnerRegistrationController extends Controller
{
    /**
     * Step 1 - Register akun owner
     * POST /api/owner/register/step-1
     */
    public function step1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string',
            'date_of_birth' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Buat user account
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'is_active' => false, // Belum aktif sampai approve
            ]);

            // Buat owner record dengan status step_1
            $owner = Owner::create([
                'user_id' => $user->id,
                'hotel_id' => null, // Akan diisi di step 2
                'registration_status' => 'step_1',
            ]);

            // Generate token untuk melanjutkan registrasi
            $token = $user->createToken('registration_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Step 1 completed successfully',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                    ],
                    'owner_id' => $owner->id,
                    'registration_status' => 'step_1',
                    'token' => $token,
                    'next_step' => 2,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to register owner',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 2 - Input data hotel dasar
     * POST /api/owner/register/step-2
     * Requires authentication token from step 1
     */
    public function step2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state_province' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'website' => 'nullable|url',
            'star_rating' => 'nullable|integer|min:1|max:5',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
            'cancellation_policy' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || $owner->registration_status !== 'step_1') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete step 1 first'
                ], 400);
            }

            DB::beginTransaction();

            // Buat hotel
            $hotel = Hotel::create([
                'name' => $request->name,
                'description' => $request->description,
                'address' => $request->address,
                'city' => $request->city,
                'state_province' => $request->state_province,
                'country' => $request->country,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'star_rating' => $request->star_rating ?? 3,
                'check_in_time' => $request->check_in_time ?? '14:00',
                'check_out_time' => $request->check_out_time ?? '12:00',
                'cancellation_policy' => $request->cancellation_policy,
                'is_active' => false, // Belum aktif sampai diapprove
            ]);

            // Update owner dengan hotel_id dan status
            $owner->update([
                'hotel_id' => $hotel->id,
                'registration_status' => 'step_2',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Step 2 completed successfully',
                'data' => [
                    'hotel' => [
                        'id' => $hotel->id,
                        'name' => $hotel->name,
                        'city' => $hotel->city,
                    ],
                    'registration_status' => 'step_2',
                    'next_step' => 3,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save hotel data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 3 - Upload fasilitas & foto hotel
     * POST /api/owner/register/step-3
     */
    public function step3(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120', // Max 5MB per image
            'images_order' => 'nullable|array',
            'images_order.*' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !in_array($owner->registration_status, ['step_2', 'step_3'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete step 2 first'
                ], 400);
            }

            DB::beginTransaction();

            $hotel = $owner->hotel;

            // Save amenities
            if ($request->has('amenities') && is_array($request->amenities)) {
                // Clear existing amenities for this registration
                HotelAmenity::where('hotel_id', $hotel->id)->delete();
                
                // Add new amenities
                foreach ($request->amenities as $amenityId) {
                    HotelAmenity::create([
                        'hotel_id' => $hotel->id,
                        'amenity_id' => $amenityId,
                    ]);
                }
            }

            // Save hotel images
            if ($request->hasFile('images')) {
                $imagesOrder = $request->images_order ?? [];
                
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('hotel-images', 'public');
                    
                    HotelImage::create([
                        'hotel_id' => $hotel->id,
                        'image_url' => $path,
                        'display_order' => $imagesOrder[$index] ?? $index,
                        'is_primary' => $index === 0, // First image as primary
                    ]);
                }
            }

            // Update status
            $owner->update([
                'registration_status' => 'step_3',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Step 3 completed successfully',
                'data' => [
                    'amenities_count' => HotelAmenity::where('hotel_id', $hotel->id)->count(),
                    'images_count' => HotelImage::where('hotel_id', $hotel->id)->count(),
                    'registration_status' => 'step_3',
                    'next_step' => 4,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save amenities and images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 4 - Upload data rekening & dokumen legalitas
     * POST /api/owner/register/step-4
     */
    public function step4(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder_name' => 'required|string|max:255',
            'business_license_number' => 'required|string|max:100',
            'business_license_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_id_number' => 'required|string|max:50',
            'tax_id_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'identity_card_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)->first();

            if (!$owner || !in_array($owner->registration_status, ['step_3', 'step_4'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete step 3 first'
                ], 400);
            }

            DB::beginTransaction();

            // Upload files
            $businessLicensePath = $request->file('business_license_file')->store('documents/business-licenses', 'public');
            $taxIdPath = $request->file('tax_id_file')->store('documents/tax-ids', 'public');
            $identityCardPath = $request->file('identity_card_file')->store('documents/identity-cards', 'public');

            // Update owner dengan data rekening dan dokumen
            $owner->update([
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'business_license_number' => $request->business_license_number,
                'business_license_file' => $businessLicensePath,
                'tax_id_number' => $request->tax_id_number,
                'tax_id_file' => $taxIdPath,
                'identity_card_file' => $identityCardPath,
                'registration_status' => 'step_4',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Step 4 completed successfully',
                'data' => [
                    'bank_name' => $owner->bank_name,
                    'account_holder_name' => $owner->account_holder_name,
                    'documents_uploaded' => true,
                    'registration_status' => 'step_4',
                    'next_step' => 5,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save banking and legal documents',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Step 5 - Konfirmasi & Submit registrasi
     * POST /api/owner/register/step-5
     */
    public function step5(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)
                ->with(['hotel.amenities', 'hotel.images'])
                ->first();

            if (!$owner || $owner->registration_status !== 'step_4') {
                return response()->json([
                    'success' => false,
                    'message' => 'Please complete step 4 first'
                ], 400);
            }

            DB::beginTransaction();

            // Update status menjadi completed dan set submitted_at
            $owner->update([
                'registration_status' => 'completed',
                'submitted_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registration completed successfully! Your application is pending admin approval.',
                'data' => [
                    'owner_id' => $owner->id,
                    'user' => [
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'email' => $user->email,
                        'phone' => $user->phone,
                    ],
                    'hotel' => [
                        'id' => $owner->hotel->id,
                        'name' => $owner->hotel->name,
                        'city' => $owner->hotel->city,
                        'country' => $owner->hotel->country,
                        'amenities_count' => $owner->hotel->amenities->count(),
                        'images_count' => $owner->hotel->images->count(),
                    ],
                    'banking' => [
                        'bank_name' => $owner->bank_name,
                        'account_holder_name' => $owner->account_holder_name,
                    ],
                    'registration_status' => 'completed',
                    'submitted_at' => $owner->submitted_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration status
     * GET /api/owner/register/status
     */
    public function getStatus(Request $request)
    {
        try {
            $user = $request->user();
            $owner = Owner::where('user_id', $user->id)
                ->with(['hotel.amenities', 'hotel.images'])
                ->first();

            if (!$owner) {
                return response()->json([
                    'success' => false,
                    'message' => 'Owner not found'
                ], 404);
            }

            $data = [
                'owner_id' => $owner->id,
                'registration_status' => $owner->registration_status,
                'current_step' => $this->getCurrentStep($owner->registration_status),
                'submitted_at' => $owner->submitted_at,
                'approved_at' => $owner->approved_at,
            ];

            // Add rejection info if rejected
            if ($owner->registration_status === 'rejected') {
                $data['rejection_reason'] = $owner->rejection_reason;
            }

            // Add hotel info if step 2 or more
            if ($owner->hotel) {
                $data['hotel'] = [
                    'id' => $owner->hotel->id,
                    'name' => $owner->hotel->name,
                    'city' => $owner->hotel->city,
                ];
            }

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
     * Helper function to get current step number
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
}
