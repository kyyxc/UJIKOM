<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerReportController extends Controller
{
    private function getOwnerHotelId()
    {
        $user = Auth::user();

        if (!$user->owner) {
            abort(403, 'Anda bukan owner.');
        }

        return $user->owner->hotel_id;
    }

    /**
     * Ringkasan laporan keuangan owner
     */
    public function summary()
    {
        $hotelId = $this->getOwnerHotelId();

        $totalIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'booked', 'checked_in', 'checked_out'])
            ->sum('total_price');

        $totalBookings = Booking::where('hotel_id', $hotelId)->count();
        $averageIncome = $totalBookings > 0 ? $totalIncome / $totalBookings : 0;

        return response()->json([
            'total_income'   => $totalIncome,
            'total_bookings' => $totalBookings,
            'average_income' => $averageIncome,
        ]);
    }

    /**
     * Laporan berdasarkan rentang tanggal
     */
    public function reportByDate(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();

        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $bookings = Booking::where('hotel_id', $hotelId)
            ->whereBetween('check_in_date', [$start, $end])
            ->whereIn('status', ['confirmed', 'booked', 'checked_in', 'checked_out'])
            ->get();

        $totalIncome = $bookings->sum('total_price');

        return response()->json([
            'start_date'     => $start,
            'end_date'       => $end,
            'total_income'   => $totalIncome,
            'total_bookings' => $bookings->count(),
            'data'           => $bookings,
        ]);
    }

    /**
     * Daftar semua booking milik owner
     */
    public function bookings()
    {
        $hotelId = $this->getOwnerHotelId();

        $bookings = Booking::with(['room', 'user', 'hotel'])
            ->where('hotel_id', $hotelId)
            ->orderBy('check_in_date', 'desc')
            ->get();

        return response()->json($bookings);
    }
}
