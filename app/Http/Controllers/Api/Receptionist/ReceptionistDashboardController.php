<?php

namespace App\Http\Controllers\Api\Receptionist;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReceptionistDashboardController extends Controller
{
    public function getDashboardStats()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;
            $today = Carbon::today();

            // Total rooms count
            $totalRooms = Room::where('hotel_id', $hotelId)->count();

            // Room status statistics
            $roomStatusStats = Room::where('hotel_id', $hotelId)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status')
                ->toArray();

            // Today's check-ins
            $todayCheckIns = Booking::where('hotel_id', $hotelId)
                ->where('check_in_date', $today)
                ->whereIn('status', ['confirmed', 'booked', 'checked_in'])
                ->count();

            // Today's check-outs
            $todayCheckOuts = Booking::where('hotel_id', $hotelId)
                ->where('check_out_date', $today)
                ->whereIn('status', ['checked_in', 'checked_out'])
                ->count();

            // Today's revenue from paid payments
            $todayRevenue = Payment::whereHas('booking', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            })
                ->whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('amount');

            // Yesterday's revenue for comparison
            $yesterdayRevenue = Payment::whereHas('booking', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            })
                ->whereDate('created_at', $today->copy()->subDay())
                ->where('status', 'paid')
                ->sum('amount');

            // Revenue trend calculation
            $revenueTrend = 0;
            if ($yesterdayRevenue > 0) {
                $revenueTrend = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => [
                        'total_rooms' => $totalRooms,
                        'available_rooms' => $roomStatusStats['available'] ?? 0,
                        'occupied_rooms' => $roomStatusStats['occupied'] ?? 0,
                        'maintenance_rooms' => $roomStatusStats['maintenance'] ?? 0,
                        'today_check_ins' => $todayCheckIns,
                        'today_check_outs' => $todayCheckOuts,
                        'today_revenue' => (float) $todayRevenue,
                        'revenue_trend' => round($revenueTrend, 2),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get room status data for pie chart
     */
    public function getRoomStatusData()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;

            $roomStatusData = Room::where('hotel_id', $hotelId)
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->map(function ($item) {
                    $colorMap = [
                        'available' => '#22c55e',    // Green
                        'occupied' => '#3b82f6',     // Blue
                        'booked' => '#f59e0b',       // Orange
                        'maintenance' => '#ef4444',  // Red
                    ];

                    $nameMap = [
                        'available' => 'Tersedia',
                        'occupied' => 'Terisi',
                        'booked' => 'Dibooking',
                        'maintenance' => 'Dibersihkan',
                    ];

                    return [
                        'name' => $nameMap[$item->status] ?? $item->status,
                        'value' => $item->count,
                        'color' => $colorMap[$item->status] ?? '#6b7280',
                        'status' => $item->status
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $roomStatusData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch room status data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reservation trends for the last 7 days
     */
    public function getReservationTrends()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;
            $endDate = Carbon::today();
            $startDate = $endDate->copy()->subDays(6);

            $trends = [];

            // Generate dates for the last 7 days
            for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
                $dayName = $date->locale('id')->translatedFormat('D');

                $checkIns = Booking::where('hotel_id', $hotelId)
                    ->whereDate('check_in_date', $date)
                    ->whereIn('status', ['confirmed', 'booked', 'checked_in'])
                    ->count();

                $checkOuts = Booking::where('hotel_id', $hotelId)
                    ->whereDate('check_out_date', $date)
                    ->whereIn('status', ['checked_in', 'checked_out'])
                    ->count();

                $trends[] = [
                    'day' => $dayName,
                    'checkIn' => $checkIns,
                    'checkOut' => $checkOuts,
                    'date' => $date->format('Y-m-d')
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $trends
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch reservation trends',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment methods data for today
     */
    public function getPaymentMethodsData()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;
            $today = Carbon::today();

            $paymentData = Payment::whereHas('booking', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            })
                ->whereDate('created_at', $today)
                ->where('status', 'paid')
                ->select('payment_method', DB::raw('SUM(amount) as total_amount'))
                ->groupBy('payment_method')
                ->get()
                ->map(function ($item) {
                    $colorMap = [
                        'cash' => '#10b981',      // Green
                        'bank_transfer' => '#3b82f6', // Blue
                        'credit_card' => '#f59e0b',   // Orange
                        'debit_card' => '#8b5cf6',    // Purple
                        'qris' => '#ef4444',      // Red
                    ];

                    $labelMap = [
                        'cash' => 'Cash',
                        'bank_transfer' => 'Transfer Bank',
                        'credit_card' => 'Kartu Kredit',
                        'debit_card' => 'Kartu Debit',
                        'qris' => 'QRIS',
                    ];

                    return [
                        'method' => $labelMap[$item->payment_method] ?? $item->payment_method,
                        'amount' => (float) $item->total_amount,
                        'color' => $colorMap[$item->payment_method] ?? '#6b7280',
                        'payment_method' => $item->payment_method
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $paymentData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment methods data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get today's activities (check-ins and check-outs)
     */
    public function getTodaysActivities()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;
            $today = Carbon::today();

            // Today's check-ins
            $checkIns = Booking::with(['room'])
                ->where('hotel_id', $hotelId)
                ->where('check_in_date', $today)
                ->whereIn('status', ['confirmed', 'booked', 'checked_in'])
                ->select('id', 'guest_name', 'room_id', 'check_in_date', 'status')
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'guest' => $booking->guest_name,
                        'room' => $booking->room->room_type . ' - ' . $booking->room->room_number,
                        'time' => '14:00', // Default check-in time
                        'status' => 'check_in',
                        'type' => 'Check-In',
                        'booking_status' => $booking->status
                    ];
                });

            // Today's check-outs
            $checkOuts = Booking::with(['room'])
                ->where('hotel_id', $hotelId)
                ->where('check_out_date', $today)
                ->whereIn('status', ['checked_in', 'checked_out'])
                ->select('id', 'guest_name', 'room_id', 'check_out_date', 'status')
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'guest' => $booking->guest_name,
                        'room' => $booking->room->room_type . ' - ' . $booking->room->room_number,
                        'time' => '12:00', // Default check-out time
                        'status' => 'check_out',
                        'type' => 'Check-Out',
                        'booking_status' => $booking->status
                    ];
                });

            // Combine and sort by time
            $activities = $checkIns->merge($checkOuts)->sortBy('time')->values();

            return response()->json([
                'success' => true,
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch today\'s activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly revenue data - FIXED for PostgreSQL
     */
    public function getMonthlyRevenue()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;
            $currentYear = Carbon::now()->year;

            // Fixed query for PostgreSQL using EXTRACT instead of MONTH
            $monthlyRevenue = Payment::whereHas('booking', function ($query) use ($hotelId) {
                $query->where('hotel_id', $hotelId);
            })
                ->whereYear('created_at', $currentYear)
                ->where('status', 'paid')
                ->select(
                    DB::raw('EXTRACT(MONTH FROM created_at) as month'),
                    DB::raw('SUM(amount) as total_revenue')
                )
                ->groupBy(DB::raw('EXTRACT(MONTH FROM created_at)'))
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    $monthNames = [
                        1 => 'Jan',
                        2 => 'Feb',
                        3 => 'Mar',
                        4 => 'Apr',
                        5 => 'Mei',
                        6 => 'Jun',
                        7 => 'Jul',
                        8 => 'Agu',
                        9 => 'Sep',
                        10 => 'Okt',
                        11 => 'Nov',
                        12 => 'Des'
                    ];

                    return [
                        'month' => $monthNames[(int)$item->month] ?? (int)$item->month,
                        'revenue' => (float) $item->total_revenue,
                        'month_number' => (int) $item->month
                    ];
                });

            // Ensure all months are represented, even if no revenue
            $allMonths = [];
            for ($i = 1; $i <= 12; $i++) {
                $monthNames = [
                    1 => 'Jan',
                    2 => 'Feb',
                    3 => 'Mar',
                    4 => 'Apr',
                    5 => 'Mei',
                    6 => 'Jun',
                    7 => 'Jul',
                    8 => 'Agu',
                    9 => 'Sep',
                    10 => 'Okt',
                    11 => 'Nov',
                    12 => 'Des'
                ];

                $existingMonth = $monthlyRevenue->firstWhere('month_number', $i);
                $allMonths[] = $existingMonth ?: [
                    'month' => $monthNames[$i],
                    'revenue' => 0,
                    'month_number' => $i
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $allMonths
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch monthly revenue data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all dashboard data in one endpoint
     */
    public function getAllDashboardData()
    {
        try {
            $stats = $this->getDashboardStats()->getData()->data->stats ?? [];
            $roomStatus = $this->getRoomStatusData()->getData()->data ?? [];
            $reservationTrends = $this->getReservationTrends()->getData()->data ?? [];
            $paymentMethods = $this->getPaymentMethodsData()->getData()->data ?? [];
            $todaysActivities = $this->getTodaysActivities()->getData()->data ?? [];
            $monthlyRevenue = $this->getMonthlyRevenue()->getData()->data ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'roomStatus' => $roomStatus,
                    'reservationTrends' => $reservationTrends,
                    'paymentMethods' => $paymentMethods,
                    'todaysActivities' => $todaysActivities,
                    'monthlyRevenue' => $monthlyRevenue,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get occupancy rate data
     */
    public function getOccupancyRate()
    {
        try {
            $hotelId = auth()->user()->receptionist->hotel_id;
            $today = Carbon::today();

            $totalRooms = Room::where('hotel_id', $hotelId)->count();
            $occupiedRooms = Room::where('hotel_id', $hotelId)
                ->whereIn('status', ['occupied', 'booked'])
                ->count();

            $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'occupancy_rate' => round($occupancyRate, 2),
                    'total_rooms' => $totalRooms,
                    'occupied_rooms' => $occupiedRooms,
                    'date' => $today->format('Y-m-d')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch occupancy rate',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
