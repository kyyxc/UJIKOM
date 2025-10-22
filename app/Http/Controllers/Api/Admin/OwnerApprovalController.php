<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\User;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerApprovalController extends Controller
{
    /**
     * Get list of pending owner registrations
     * GET /api/admin/owner-registrations
     */
    public function index(Request $request)
    {
        try {
            $status = $request->query('status', 'completed'); // Default to completed (pending approval)
            $perPage = $request->query('per_page', 15);

            $query = Owner::with(['user', 'hotel'])
                ->whereIn('registration_status', ['completed', 'approved', 'rejected']);

            if ($status && $status !== 'all') {
                $query->where('registration_status', $status);
            }

            $registrations = $query->orderBy('submitted_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $registrations
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get registrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single owner registration detail
     * GET /api/admin/owner-registrations/{id}
     */
    public function show($id)
    {
        try {
            $owner = Owner::with(['user', 'hotel.amenities', 'hotel.images'])
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'owner_id' => $owner->id,
                    'registration_status' => $owner->registration_status,
                    'submitted_at' => $owner->submitted_at,
                    'approved_at' => $owner->approved_at,
                    'rejection_reason' => $owner->rejection_reason,
                    'user' => [
                        'id' => $owner->user->id,
                        'name' => $owner->user->first_name . ' ' . $owner->user->last_name,
                        'email' => $owner->user->email,
                        'phone' => $owner->user->phone,
                        'address' => $owner->user->address,
                        'date_of_birth' => $owner->user->date_of_birth,
                    ],
                    'hotel' => [
                        'id' => $owner->hotel->id,
                        'name' => $owner->hotel->name,
                        'description' => $owner->hotel->description,
                        'address' => $owner->hotel->address,
                        'city' => $owner->hotel->city,
                        'state_province' => $owner->hotel->state_province,
                        'country' => $owner->hotel->country,
                        'email' => $owner->hotel->email,
                        'phone' => $owner->hotel->phone,
                        'website' => $owner->hotel->website,
                        'star_rating' => $owner->hotel->star_rating,
                        'amenities' => $owner->hotel->amenities,
                        'images' => $owner->hotel->images,
                    ],
                    'banking' => [
                        'bank_name' => $owner->bank_name,
                        'account_number' => $owner->account_number,
                        'account_holder_name' => $owner->account_holder_name,
                    ],
                    'legal_documents' => [
                        'business_license_number' => $owner->business_license_number,
                        'business_license_file' => $owner->business_license_file ? asset('storage/' . $owner->business_license_file) : null,
                        'tax_id_number' => $owner->tax_id_number,
                        'tax_id_file' => $owner->tax_id_file ? asset('storage/' . $owner->tax_id_file) : null,
                        'identity_card_file' => $owner->identity_card_file ? asset('storage/' . $owner->identity_card_file) : null,
                    ],
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Owner registration not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Approve owner registration
     * POST /api/admin/owner-registrations/{id}/approve
     */
    public function approve($id)
    {
        try {
            $owner = Owner::with(['user', 'hotel'])->findOrFail($id);

            if ($owner->registration_status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only completed registrations can be approved'
                ], 400);
            }

            DB::beginTransaction();

            // Update owner status
            $owner->update([
                'registration_status' => 'approved',
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            // Activate user account
            $owner->user->update([
                'is_active' => true,
            ]);

            // Activate hotel
            $owner->hotel->update([
                'is_active' => true,
            ]);

            DB::commit();

            // TODO: Send email notification to owner

            return response()->json([
                'success' => true,
                'message' => 'Owner registration approved successfully',
                'data' => [
                    'owner_id' => $owner->id,
                    'registration_status' => 'approved',
                    'approved_at' => $owner->approved_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject owner registration
     * POST /api/admin/owner-registrations/{id}/reject
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        try {
            $owner = Owner::with(['user', 'hotel'])->findOrFail($id);

            if ($owner->registration_status !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only completed registrations can be rejected'
                ], 400);
            }

            DB::beginTransaction();

            // Update owner status
            $owner->update([
                'registration_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'approved_at' => null,
            ]);

            // Keep user and hotel inactive
            $owner->user->update([
                'is_active' => false,
            ]);

            $owner->hotel->update([
                'is_active' => false,
            ]);

            DB::commit();

            // TODO: Send email notification to owner with rejection reason

            return response()->json([
                'success' => true,
                'message' => 'Owner registration rejected',
                'data' => [
                    'owner_id' => $owner->id,
                    'registration_status' => 'rejected',
                    'rejection_reason' => $owner->rejection_reason,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject registration',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get registration statistics
     * GET /api/admin/owner-registrations/statistics
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => Owner::count(),
                'pending' => Owner::where('registration_status', 'completed')->count(),
                'approved' => Owner::where('registration_status', 'approved')->count(),
                'rejected' => Owner::where('registration_status', 'rejected')->count(),
                'incomplete' => Owner::whereIn('registration_status', ['pending', 'step_1', 'step_2', 'step_3', 'step_4'])->count(),
                'today_submissions' => Owner::where('registration_status', 'completed')
                    ->whereDate('submitted_at', today())
                    ->count(),
                'this_week_submissions' => Owner::where('registration_status', 'completed')
                    ->whereBetween('submitted_at', [now()->startOfWeek(), now()->endOfWeek()])
                    ->count(),
                'this_month_submissions' => Owner::where('registration_status', 'completed')
                    ->whereMonth('submitted_at', now()->month)
                    ->whereYear('submitted_at', now()->year)
                    ->count(),
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
     * Get recent submissions (last 10)
     * GET /api/admin/owner-registrations/recent
     */
    public function recent()
    {
        try {
            $recentSubmissions = Owner::with(['user', 'hotel'])
                ->where('registration_status', 'completed')
                ->orderBy('submitted_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($owner) {
                    return [
                        'id' => $owner->id,
                        'owner_name' => $owner->user->first_name . ' ' . $owner->user->last_name,
                        'hotel_name' => $owner->hotel->name,
                        'hotel_city' => $owner->hotel->city,
                        'submitted_at' => $owner->submitted_at,
                        'days_waiting' => now()->diffInDays($owner->submitted_at),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $recentSubmissions
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent submissions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search owner registrations
     * GET /api/admin/owner-registrations/search
     */
    public function search(Request $request)
    {
        try {
            $query = Owner::with(['user', 'hotel']);

            // Search by owner name
            if ($request->has('owner_name')) {
                $ownerName = $request->owner_name;
                $query->whereHas('user', function ($q) use ($ownerName) {
                    $q->where('first_name', 'like', '%' . $ownerName . '%')
                      ->orWhere('last_name', 'like', '%' . $ownerName . '%');
                });
            }

            // Search by hotel name
            if ($request->has('hotel_name')) {
                $hotelName = $request->hotel_name;
                $query->whereHas('hotel', function ($q) use ($hotelName) {
                    $q->where('name', 'like', '%' . $hotelName . '%');
                });
            }

            // Filter by city
            if ($request->has('city')) {
                $city = $request->city;
                $query->whereHas('hotel', function ($q) use ($city) {
                    $q->where('city', 'like', '%' . $city . '%');
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $query->where('registration_status', $request->status);
            }

            // Filter by date range
            if ($request->has('date_from')) {
                $query->whereDate('submitted_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('submitted_at', '<=', $request->date_to);
            }

            $perPage = $request->query('per_page', 15);
            $results = $query->orderBy('submitted_at', 'desc')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $results
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search registrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk approve registrations
     * POST /api/admin/owner-registrations/bulk-approve
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'owner_ids' => 'required|array',
            'owner_ids.*' => 'exists:owners,id',
        ]);

        try {
            DB::beginTransaction();

            $approvedCount = 0;
            $failedIds = [];

            foreach ($request->owner_ids as $ownerId) {
                $owner = Owner::with(['user', 'hotel'])->find($ownerId);

                if ($owner && $owner->registration_status === 'completed') {
                    // Update owner status
                    $owner->update([
                        'registration_status' => 'approved',
                        'approved_at' => now(),
                        'rejection_reason' => null,
                    ]);

                    // Activate user account
                    $owner->user->update(['is_active' => true]);

                    // Activate hotel
                    $owner->hotel->update(['is_active' => true]);

                    $approvedCount++;
                } else {
                    $failedIds[] = $ownerId;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$approvedCount} registrations approved successfully",
                'data' => [
                    'approved_count' => $approvedCount,
                    'failed_ids' => $failedIds,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk approve registrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk reject registrations
     * POST /api/admin/owner-registrations/bulk-reject
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'owner_ids' => 'required|array',
            'owner_ids.*' => 'exists:owners,id',
            'rejection_reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $rejectedCount = 0;
            $failedIds = [];

            foreach ($request->owner_ids as $ownerId) {
                $owner = Owner::with(['user', 'hotel'])->find($ownerId);

                if ($owner && $owner->registration_status === 'completed') {
                    // Update owner status
                    $owner->update([
                        'registration_status' => 'rejected',
                        'rejection_reason' => $request->rejection_reason,
                        'approved_at' => null,
                    ]);

                    // Keep user and hotel inactive
                    $owner->user->update(['is_active' => false]);
                    $owner->hotel->update(['is_active' => false]);

                    $rejectedCount++;
                } else {
                    $failedIds[] = $ownerId;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$rejectedCount} registrations rejected successfully",
                'data' => [
                    'rejected_count' => $rejectedCount,
                    'failed_ids' => $failedIds,
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to bulk reject registrations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get approval history with filters
     * GET /api/admin/owner-registrations/history
     */
    public function history(Request $request)
    {
        try {
            $query = Owner::with(['user', 'hotel'])
                ->whereIn('registration_status', ['approved', 'rejected'])
                ->orderBy('approved_at', 'desc');

            // Filter by action (approved/rejected)
            if ($request->has('action')) {
                $query->where('registration_status', $request->action);
            }

            // Filter by date range
            if ($request->has('date_from')) {
                $query->whereDate('approved_at', '>=', $request->date_from);
            }
            if ($request->has('date_to')) {
                $query->whereDate('approved_at', '<=', $request->date_to);
            }

            $perPage = $request->query('per_page', 15);
            $history = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $history
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get approval history',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download document
     * GET /api/admin/owner-registrations/{id}/download/{document_type}
     * document_type: business_license, tax_id, identity_card
     */
    public function downloadDocument($id, $documentType)
    {
        try {
            $owner = Owner::findOrFail($id);

            $fileMap = [
                'business_license' => $owner->business_license_file,
                'tax_id' => $owner->tax_id_file,
                'identity_card' => $owner->identity_card_file,
            ];

            if (!isset($fileMap[$documentType])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid document type'
                ], 400);
            }

            $filePath = $fileMap[$documentType];

            if (!$filePath || !\Storage::disk('public')->exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document not found'
                ], 404);
            }

            return \Storage::disk('public')->download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to download document',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get waiting time analysis
     * GET /api/admin/owner-registrations/waiting-analysis
     */
    public function waitingAnalysis()
    {
        try {
            $pendingRegistrations = Owner::where('registration_status', 'completed')
                ->get()
                ->map(function ($owner) {
                    $daysWaiting = now()->diffInDays($owner->submitted_at);
                    return [
                        'days_waiting' => $daysWaiting,
                        'priority' => $daysWaiting > 7 ? 'high' : ($daysWaiting > 3 ? 'medium' : 'low'),
                    ];
                });

            $analysis = [
                'total_pending' => $pendingRegistrations->count(),
                'high_priority' => $pendingRegistrations->where('priority', 'high')->count(), // > 7 days
                'medium_priority' => $pendingRegistrations->where('priority', 'medium')->count(), // 3-7 days
                'low_priority' => $pendingRegistrations->where('priority', 'low')->count(), // < 3 days
                'average_waiting_days' => $pendingRegistrations->avg('days_waiting') ?? 0,
                'longest_waiting_days' => $pendingRegistrations->max('days_waiting') ?? 0,
            ];

            return response()->json([
                'success' => true,
                'data' => $analysis
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get waiting analysis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
