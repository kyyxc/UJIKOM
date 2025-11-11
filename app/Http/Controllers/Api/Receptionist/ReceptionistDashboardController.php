<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceptionistDashboardController extends Controller
{
    private function getHotelId()
    {
        return auth()->user()->receptionist->hotel_id;
    }

    private function getHotelBookingQuery()
    {
        return Booking::where('hotel_id', $this->getHotelId());
    }

    private function getHotelPaymentQuery()
    {
        return Payment::whereHas('booking', function ($query) {
            $query->where('hotel_id', $this->getHotelId());
        });
    }

    public function getAllDashboardData()
    {
        $hotelId = $this->getHotelId();
        $today = Carbon::today();

        // Stats
        $roomStatusStats = Room::where('hotel_id', $hotelId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $todayRevenue = $this->getHotelPaymentQuery()
            ->whereDate('created_at', $today)
            ->where('status', 'paid')
            ->sum('amount');

        $yesterdayRevenue = $this->getHotelPaymentQuery()
            ->whereDate('created_at', $today->copy()->subDay())
            ->where('status', 'paid')
            ->sum('amount');

        $revenueTrend = $yesterdayRevenue > 0 
            ? (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 
            : 0;

        // Room Status
        $roomStatus = Room::where('hotel_id', $hotelId)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                $maps = [
                    'available' => ['name' => 'Tersedia', 'color' => '#22c55e'],
                    'occupied' => ['name' => 'Terisi', 'color' => '#3b82f6'],
                    'booked' => ['name' => 'Dibooking', 'color' => '#f59e0b'],
                    'maintenance' => ['name' => 'Dibersihkan', 'color' => '#ef4444'],
                ];
                return [
                    'name' => $maps[$item->status]['name'] ?? $item->status,
                    'value' => $item->count,
                    'color' => $maps[$item->status]['color'] ?? '#6b7280',
                ];
            });

        // Reservation Trends (7 days)
        $trends = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $trends->push([
                'day' => $date->format('D'),
                'checkIn' => $this->getHotelBookingQuery()
                    ->whereDate('check_in_date', $date)
                    ->whereIn('status', ['confirmed', 'booked', 'checked_in'])
                    ->count(),
                'checkOut' => $this->getHotelBookingQuery()
                    ->whereDate('check_out_date', $date)
                    ->whereIn('status', ['checked_in', 'checked_out'])
                    ->count(),
            ]);
        }

        // Monthly Revenue
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $revenueData = $this->getHotelPaymentQuery()
            ->whereYear('created_at', $today->year)
            ->where('status', 'paid')
            ->select(DB::raw('EXTRACT(MONTH FROM created_at) as month'), DB::raw('SUM(amount) as revenue'))
            ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
            ->pluck('revenue', 'month');

        $monthlyRevenue = collect(range(1, 12))->map(function ($month) use ($monthNames, $revenueData) {
            return [
                'month' => $monthNames[$month - 1],
                'revenue' => (float) ($revenueData[$month] ?? 0),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_rooms' => Room::where('hotel_id', $hotelId)->count(),
                    'available_rooms' => $roomStatusStats['available'] ?? 0,
                    'occupied_rooms' => $roomStatusStats['occupied'] ?? 0,
                    'maintenance_rooms' => $roomStatusStats['maintenance'] ?? 0,
                    'today_check_ins' => $this->getHotelBookingQuery()
                        ->where('check_in_date', $today)
                        ->whereIn('status', ['confirmed', 'booked', 'checked_in'])
                        ->count(),
                    'today_check_outs' => $this->getHotelBookingQuery()
                        ->where('check_out_date', $today)
                        ->whereIn('status', ['checked_in', 'checked_out'])
                        ->count(),
                    'today_revenue' => (float) $todayRevenue,
                    'revenue_trend' => round($revenueTrend, 2),
                ],
                'roomStatus' => $roomStatus,
                'reservationTrends' => $trends,
                'monthlyRevenue' => $monthlyRevenue,
            ]
        ]);
    }
}
