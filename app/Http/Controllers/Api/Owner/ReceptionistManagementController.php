<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Receptionist;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ReceptionistManagementController extends Controller
{
    /**
     * Get all receptionists for the owner's hotel
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get owner data
            $owner = Owner::where('user_id', $user->id)->first();
            
            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner hotel not found',
                ], 404);
            }

            // Get all receptionists for this hotel
            $receptionists = Receptionist::with('user')
                ->where('hotel_id', $owner->hotel_id)
                ->get()
                ->map(function ($receptionist) {
                    return [
                        'id' => $receptionist->id,
                        'user_id' => $receptionist->user_id,
                        'hotel_id' => $receptionist->hotel_id,
                        'email' => $receptionist->user->email,
                        'first_name' => $receptionist->user->first_name,
                        'last_name' => $receptionist->user->last_name,
                        'phone' => $receptionist->user->phone,
                        'address' => $receptionist->user->address,
                        'created_at' => $receptionist->created_at,
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Get receptionists success',
                'data' => $receptionists,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get receptionists',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create new receptionist for the owner's hotel
     */
    public function store(Request $request)
    {
        try {
            $user = $request->user();
            
            // Get owner data
            $owner = Owner::where('user_id', $user->id)->first();
            
            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner hotel not found',
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Create user account
            $receptionistUser = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'profile' => null,
            ]);

            // Create receptionist record
            $receptionist = Receptionist::create([
                'user_id' => $receptionistUser->id,
                'hotel_id' => $owner->hotel_id,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Receptionist created successfully',
                'data' => [
                    'id' => $receptionist->id,
                    'user_id' => $receptionistUser->id,
                    'hotel_id' => $receptionist->hotel_id,
                    'email' => $receptionistUser->email,
                    'first_name' => $receptionistUser->first_name,
                    'last_name' => $receptionistUser->last_name,
                    'phone' => $receptionistUser->phone,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create receptionist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get receptionist detail
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Get owner data
            $owner = Owner::where('user_id', $user->id)->first();
            
            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner hotel not found',
                ], 404);
            }

            // Get receptionist (must be from owner's hotel)
            $receptionist = Receptionist::with('user')
                ->where('id', $id)
                ->where('hotel_id', $owner->hotel_id)
                ->first();

            if (!$receptionist) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Receptionist not found',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Get receptionist detail success',
                'data' => [
                    'id' => $receptionist->id,
                    'user_id' => $receptionist->user_id,
                    'hotel_id' => $receptionist->hotel_id,
                    'email' => $receptionist->user->email,
                    'first_name' => $receptionist->user->first_name,
                    'last_name' => $receptionist->user->last_name,
                    'phone' => $receptionist->user->phone,
                    'address' => $receptionist->user->address,
                    'date_of_birth' => $receptionist->user->date_of_birth,
                    'created_at' => $receptionist->created_at,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to get receptionist detail',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update receptionist data
     */
    public function update(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Get owner data
            $owner = Owner::where('user_id', $user->id)->first();
            
            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner hotel not found',
                ], 404);
            }

            // Get receptionist (must be from owner's hotel)
            $receptionist = Receptionist::with('user')
                ->where('id', $id)
                ->where('hotel_id', $owner->hotel_id)
                ->first();

            if (!$receptionist) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Receptionist not found',
                ], 404);
            }

            // Validate request
            $validator = Validator::make($request->all(), [
                'email' => 'sometimes|email|unique:users,email,' . $receptionist->user_id,
                'password' => 'sometimes|min:8',
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|string|max:20',
                'address' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Update user data
            $updateData = [];
            if ($request->has('email')) $updateData['email'] = $request->email;
            if ($request->has('password')) $updateData['password'] = Hash::make($request->password);
            if ($request->has('first_name')) $updateData['first_name'] = $request->first_name;
            if ($request->has('last_name')) $updateData['last_name'] = $request->last_name;
            if ($request->has('phone')) $updateData['phone'] = $request->phone;
            if ($request->has('address')) $updateData['address'] = $request->address;
            if ($request->has('date_of_birth')) $updateData['date_of_birth'] = $request->date_of_birth;

            $receptionist->user->update($updateData);

            return response()->json([
                'status' => 'success',
                'message' => 'Receptionist updated successfully',
                'data' => [
                    'id' => $receptionist->id,
                    'user_id' => $receptionist->user_id,
                    'hotel_id' => $receptionist->hotel_id,
                    'email' => $receptionist->user->email,
                    'first_name' => $receptionist->user->first_name,
                    'last_name' => $receptionist->user->last_name,
                    'phone' => $receptionist->user->phone,
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update receptionist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete receptionist
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Get owner data
            $owner = Owner::where('user_id', $user->id)->first();
            
            if (!$owner || !$owner->hotel_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Owner hotel not found',
                ], 404);
            }

            // Get receptionist (must be from owner's hotel)
            $receptionist = Receptionist::where('id', $id)
                ->where('hotel_id', $owner->hotel_id)
                ->first();

            if (!$receptionist) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Receptionist not found',
                ], 404);
            }

            // Delete receptionist and user account
            $userId = $receptionist->user_id;
            $receptionist->delete();
            User::find($userId)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Receptionist deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete receptionist',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
