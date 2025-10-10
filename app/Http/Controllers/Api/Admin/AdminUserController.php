<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::with(['admin', 'owner.hotel', 'receptionist.hotel']);

            // Search filter
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Role filter
            if ($request->has('role') && $request->role !== 'all') {
                switch ($request->role) {
                    case 'admin':
                        $query->whereHas('admin');
                        break;
                    case 'owner':
                        $query->whereHas('owner');
                        break;
                    case 'receptionist':
                        $query->whereHas('receptionist');
                        break;
                    case 'user':
                        $query->whereDoesntHave('admin')
                            ->whereDoesntHave('owner')
                            ->whereDoesntHave('receptionist');
                        break;
                }
            }

            // Status filter
            if ($request->has('status') && $request->status !== 'all') {
                $query->where('is_active', $request->status === 'active');
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $users = $query->latest()->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'admins' => User::whereHas('admin')->count(),
                'owners' => User::whereHas('owner')->count(),
                'receptionists' => User::whereHas('receptionist')->count(),
                'pending_verification' => User::whereNull('email_verified_at')->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'role' => 'required|in:user,admin,owner,receptionist',
                'is_active' => 'boolean',
                'password' => 'required|string|min:8',
                'hotel_id' => 'required_if:role,owner,receptionist|exists:hotels,id',
                'shift' => 'required_if:role,receptionist|string'
            ]);

            // Create user
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            // Assign role
            switch ($validated['role']) {
                case 'admin':
                    Admin::create(['user_id' => $user->id]);
                    break;

                case 'owner':
                    Owner::create([
                        'user_id' => $user->id,
                        'hotel_id' => $validated['hotel_id']
                    ]);
                    break;

                case 'receptionist':
                    Receptionist::create([
                        'user_id' => $user->id,
                        'hotel_id' => $validated['hotel_id'],
                        'shift' => $validated['shift']
                    ]);
                    break;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load(['admin', 'owner.hotel', 'receptionist.hotel'])
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        try {
            $user->load(['admin', 'owner.hotel', 'receptionist.hotel']);

            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'first_name' => 'sometimes|required|string|max:255',
                'last_name' => 'sometimes|required|string|max:255',
                'email' => [
                    'sometimes',
                    'required',
                    'email',
                    Rule::unique('users')->ignore($user->id)
                ],
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'role' => 'sometimes|required|in:user,admin,owner,receptionist',
                'is_active' => 'sometimes|boolean',
                'password' => 'nullable|string|min:8',
                'hotel_id' => 'required_if:role,owner,receptionist|exists:hotels,id',
                'shift' => 'required_if:role,receptionist|string'
            ]);

            // Update user basic info
            $userData = [
                'first_name' => $validated['first_name'] ?? $user->first_name,
                'last_name' => $validated['last_name'] ?? $user->last_name,
                'email' => $validated['email'] ?? $user->email,
                'phone' => $validated['phone'] ?? $user->phone,
                'address' => $validated['address'] ?? $user->address,
                'date_of_birth' => $validated['date_of_birth'] ?? $user->date_of_birth,
                'is_active' => $validated['is_active'] ?? $user->is_active,
            ];

            if (isset($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $user->update($userData);

            // Update role if changed
            if (isset($validated['role'])) {
                // Remove existing roles
                $user->admin()->delete();
                $user->owner()->delete();
                $user->receptionist()->delete();

                // Assign new role
                switch ($validated['role']) {
                    case 'admin':
                        Admin::create(['user_id' => $user->id]);
                        break;

                    case 'owner':
                        Owner::create([
                            'user_id' => $user->id,
                            'hotel_id' => $validated['hotel_id']
                        ]);
                        break;

                    case 'receptionist':
                        Receptionist::create([
                            'user_id' => $user->id,
                            'hotel_id' => $validated['hotel_id'],
                            'shift' => $validated['shift']
                        ]);
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load(['admin', 'owner.hotel', 'receptionist.hotel'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            $userName = $user->first_name . ' ' . $user->last_name;
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "User {$userName} deleted successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk actions (activate/deactivate/delete)
     */
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'user_ids' => 'required|array',
                'user_ids.*' => 'exists:users,id',
                'action' => 'required|in:activate,deactivate,delete'
            ]);

            $userIds = $request->user_ids;

            switch ($request->action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['is_active' => true]);
                    $message = 'Users activated successfully';
                    break;

                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['is_active' => false]);
                    $message = 'Users deactivated successfully';
                    break;

                case 'delete':
                    User::whereIn('id', $userIds)->delete();
                    $message = 'Users deleted successfully';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => count($userIds)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
