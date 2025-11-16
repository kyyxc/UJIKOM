<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['guest', 'hotel', 'room.images', 'payment'])
            ->latest();

        // Optional search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%$search%")
                    ->orWhere('guest_email', 'like', "%$search%")
                    ->orWhere('guest_phone', 'like', "%$search%")
                    ->orWhereHas('hotel', function ($hotelQuery) use ($search) {
                        $hotelQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        // Optional filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Optional filter by source (online/offline)
        if ($request->has('source') && !empty($request->source)) {
            $query->where('source', $request->source);
        }

        // Optional filter by hotel
        if ($request->has('hotel_id') && !empty($request->hotel_id)) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Optional filter by date range
        if ($request->has('check_in_from') && !empty($request->check_in_from)) {
            $query->where('check_in_date', '>=', $request->check_in_from);
        }

        if ($request->has('check_in_to') && !empty($request->check_in_to)) {
            $query->where('check_in_date', '<=', $request->check_in_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['created_at', 'check_in_date', 'check_out_date', 'total_price', 'status'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = $request->get('per_page', 10);
        $bookings = $query->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'All bookings retrieved successfully',
            'data' => $bookings,
        ]);
    }
}
