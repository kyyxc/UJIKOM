<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function getDashboardStats()
    {
        try {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $previousMonth = Carbon::now()->subMonth()->month;
            $previousYear = Carbon::now()->subMonth()->year;

            // Total hotels
            $totalHotels = Hotel::count();
            $previousMonthHotels = Hotel::whereYear('created_at', $previousYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();
            $hotelTrend = $previousMonthHotels > 0 ?
                (($totalHotels - $previousMonthHotels) / $previousMonthHotels) * 100 : 0;

            // Total rooms
            $totalRooms = Room::count();
            $previousMonthRooms = Room::whereYear('created_at', $previousYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();
            $roomTrend = $previousMonthRooms > 0 ?
                (($totalRooms - $previousMonthRooms) / $previousMonthRooms) * 100 : 0;

            // Total bookings this month
            $currentMonthBookings = Booking::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->count();
            $previousMonthBookings = Booking::whereYear('created_at', $previousYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();
            $bookingTrend = $previousMonthBookings > 0 ?
                (($currentMonthBookings - $previousMonthBookings) / $previousMonthBookings) * 100 : 0;

            // Total users
            $totalUsers = User::count();
            $previousMonthUsers = User::whereYear('created_at', $previousYear)
                ->whereMonth('created_at', $previousMonth)
                ->count();
            $userTrend = $previousMonthUsers > 0 ?
                (($totalUsers - $previousMonthUsers) / $previousMonthUsers) * 100 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'total_hotels' => $totalHotels,
                    'total_rooms' => $totalRooms,
                    'current_month_bookings' => $currentMonthBookings,
                    'total_users' => $totalUsers,
                    'trends' => [
                        'hotels' => round($hotelTrend, 1),
                        'rooms' => round($roomTrend, 1),
                        'bookings' => round($bookingTrend, 1),
                        'users' => round($userTrend, 1),
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
     * Get reservations and revenue chart data
     */
    public function getReservationsRevenueChart()
    {
        try {
            $currentYear = Carbon::now()->year;

            $chartData = DB::table('bookings')
                ->join('payments', 'bookings.id', '=', 'payments.booking_id')
                ->whereYear('bookings.created_at', $currentYear)
                ->where('payments.status', 'paid')
                ->select(
                    DB::raw('EXTRACT(MONTH FROM bookings.created_at) as month'),
                    DB::raw('COUNT(DISTINCT bookings.id) as reservations'),
                    DB::raw('SUM(payments.amount) as revenue')
                )
                ->groupBy(DB::raw('EXTRACT(MONTH FROM bookings.created_at)'))
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
                        'reservations' => (int)$item->reservations,
                        'revenue' => (float)$item->revenue,
                        'month_number' => (int)$item->month
                    ];
                });

            // Ensure all months are represented
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

                $existingMonth = $chartData->firstWhere('month_number', $i);
                $allMonths[] = $existingMonth ?: [
                    'month' => $monthNames[$i],
                    'reservations' => 0,
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
                'message' => 'Failed to fetch chart data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recent reservations
     */
    public function getRecentReservations()
    {
        try {
            $recentReservations = Booking::with(['room.hotel', 'guest'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($booking) {
                    $nights = Carbon::parse($booking->check_in_date)
                        ->diffInDays(Carbon::parse($booking->check_out_date));

                    return [
                        'id' => $booking->id,
                        'guest' => $booking->guest_name,
                        'room' => $booking->room->room_type . ' - ' . $booking->room->room_number,
                        'hotel' => $booking->room->hotel->name,
                        'date' => Carbon::parse($booking->created_at)->format('d M Y'),
                        'nights' => $nights,
                        'amount' => (float)$booking->total_price,
                        'status' => $booking->status,
                        'source' => $booking->source,
                        'check_in_date' => $booking->check_in_date,
                        'check_out_date' => $booking->check_out_date,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $recentReservations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch recent reservations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hotel performance data
     */
    public function getHotelPerformance()
    {
        try {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $hotelPerformance = Hotel::withCount(['rooms', 'bookings' => function ($query) use ($currentMonth, $currentYear) {
                $query->whereYear('created_at', $currentYear)
                    ->whereMonth('created_at', $currentMonth);
            }])
                ->withSum(['bookings' => function ($query) use ($currentMonth, $currentYear) {
                    $query->whereYear('created_at', $currentYear)
                        ->whereMonth('created_at', $currentMonth);
                }], 'total_price')
                ->orderBy('bookings_sum_total_price', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($hotel) {
                    return [
                        'id' => $hotel->id,
                        'name' => $hotel->name,
                        'total_rooms' => $hotel->rooms_count,
                        'monthly_bookings' => $hotel->bookings_count,
                        'monthly_revenue' => (float)$hotel->bookings_sum_total_price,
                        'location' => $hotel->address,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $hotelPerformance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch hotel performance data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking status distribution
     */
    public function getBookingStatusDistribution()
    {
        try {
            $statusDistribution = Booking::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->map(function ($item) {
                    $statusLabels = [
                        'pending' => 'Menunggu',
                        'confirmed' => 'Dikonfirmasi',
                        'booked' => 'Dibooking',
                        'checked_in' => 'Check-in',
                        'checked_out' => 'Check-out',
                        'cancelled' => 'Dibatalkan',
                    ];

                    $colorMap = [
                        'pending' => '#f59e0b',    // Orange
                        'confirmed' => '#3b82f6',  // Blue
                        'booked' => '#10b981',     // Green
                        'checked_in' => '#8b5cf6', // Purple
                        'checked_out' => '#6b7280', // Gray
                        'cancelled' => '#ef4444',  // Red
                    ];

                    return [
                        'status' => $item->status,
                        'label' => $statusLabels[$item->status] ?? $item->status,
                        'count' => $item->count,
                        'color' => $colorMap[$item->status] ?? '#6b7280',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $statusDistribution
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch booking status distribution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment methods summary
     */
    public function getPaymentMethodsSummary()
    {
        try {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            $paymentMethods = Payment::where('status', 'paid')
                ->whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->select('payment_method', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method')
                ->get()
                ->map(function ($item) {
                    $labelMap = [
                        'cash' => 'Cash',
                        'bank_transfer' => 'Transfer Bank',
                        'credit_card' => 'Kartu Kredit',
                        'debit_card' => 'Kartu Debit',
                        'qris' => 'QRIS',
                    ];

                    $colorMap = [
                        'cash' => '#10b981',      // Green
                        'bank_transfer' => '#3b82f6', // Blue
                        'credit_card' => '#f59e0b',   // Orange
                        'debit_card' => '#8b5cf6',    // Purple
                        'qris' => '#ef4444',      // Red
                    ];

                    return [
                        'method' => $item->payment_method,
                        'label' => $labelMap[$item->payment_method] ?? $item->payment_method,
                        'total_amount' => (float)$item->total_amount,
                        'count' => $item->count,
                        'color' => $colorMap[$item->payment_method] ?? '#6b7280',
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $paymentMethods
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment methods summary',
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
            $stats = $this->getDashboardStats()->getData()->data ?? [];
            $chartData = $this->getReservationsRevenueChart()->getData()->data ?? [];
            $recentReservations = $this->getRecentReservations()->getData()->data ?? [];
            $hotelPerformance = $this->getHotelPerformance()->getData()->data ?? [];
            $statusDistribution = $this->getBookingStatusDistribution()->getData()->data ?? [];
            $paymentMethods = $this->getPaymentMethodsSummary()->getData()->data ?? [];

            return response()->json([
                'success' => true,
                'data' => [
                    'stats' => $stats,
                    'chartData' => $chartData,
                    'recentReservations' => $recentReservations,
                    'hotelPerformance' => $hotelPerformance,
                    'statusDistribution' => $statusDistribution,
                    'paymentMethods' => $paymentMethods,
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
     * Get quick stats for the current month
     */
    public function getQuickStats()
    {
        try {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $today = Carbon::today();

            // Today's revenue
            $todayRevenue = Payment::whereDate('created_at', $today)
                ->where('status', 'paid')
                ->sum('amount');

            // This month's revenue
            $monthRevenue = Payment::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $currentMonth)
                ->where('status', 'paid')
                ->sum('amount');

            // Active bookings today (checked in)
            $activeBookings = Booking::where('check_in_date', '<=', $today)
                ->where('check_out_date', '>=', $today)
                ->whereIn('status', ['checked_in', 'confirmed', 'booked'])
                ->count();

            // Occupancy rate
            $totalRooms = Room::count();
            $occupiedRooms = Room::whereIn('status', ['occupied', 'booked'])->count();
            $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'today_revenue' => (float)$todayRevenue,
                    'month_revenue' => (float)$monthRevenue,
                    'active_bookings' => $activeBookings,
                    'occupancy_rate' => round($occupancyRate, 2),
                    'total_rooms' => $totalRooms,
                    'occupied_rooms' => $occupiedRooms,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch quick stats',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
