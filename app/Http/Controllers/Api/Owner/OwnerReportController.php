<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
     * Get comprehensive financial summary
     */
    public function financialSummary(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();

        // Get filter parameters
        $year = $request->query('year', date('Y'));
        $month = $request->query('month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        // Set date range
        if ($startDate && $endDate) {
            $dateFrom = Carbon::parse($startDate);
            $dateTo = Carbon::parse($endDate);
        } elseif ($month) {
            $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
            $dateTo = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $dateFrom = Carbon::create($year, 1, 1)->startOfYear();
            $dateTo = Carbon::create($year, 12, 31)->endOfYear();
        }

        // Calculate previous period for comparison
        $periodDays = $dateFrom->diffInDays($dateTo);
        $prevDateFrom = $dateFrom->copy()->subDays($periodDays + 1);
        $prevDateTo = $dateFrom->copy()->subDay();

        // Get current period income
        $totalIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->sum('total_price');

        // Get previous period income
        $prevIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$prevDateFrom, $prevDateTo])
            ->sum('total_price');

        // Get current period expenses
        $totalExpenses = Expense::where('hotel_id', $hotelId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->sum('amount');

        // Get previous period expenses
        $prevExpenses = Expense::where('hotel_id', $hotelId)
            ->whereBetween('date', [$prevDateFrom, $prevDateTo])
            ->sum('amount');

        // Calculate metrics
        $netProfit = $totalIncome - $totalExpenses;
        $profitMargin = $totalIncome > 0 ? ($netProfit / $totalIncome) * 100 : 0;
        
        $incomeGrowth = $prevIncome > 0 
            ? (($totalIncome - $prevIncome) / $prevIncome) * 100 
            : 0;
        
        $expenseGrowth = $prevExpenses > 0 
            ? (($totalExpenses - $prevExpenses) / $prevExpenses) * 100 
            : 0;

        // Get total bookings
        $totalBookings = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->count();

        return response()->json([
            'data' => [
                'total_income' => (float) $totalIncome,
                'total_expenses' => (float) $totalExpenses,
                'net_profit' => (float) $netProfit,
                'profit_margin' => round($profitMargin, 2),
                'total_bookings' => $totalBookings,
                'income_growth' => round($incomeGrowth, 2),
                'expense_growth' => round($expenseGrowth, 2),
                'period' => [
                    'from' => $dateFrom->format('Y-m-d'),
                    'to' => $dateTo->format('Y-m-d'),
                ],
            ],
        ], 200);
    }

    /**
     * Get monthly financial trend data
     */
    public function monthlyTrend(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();
        $year = $request->query('year', date('Y'));

        $monthlyData = [];

        for ($month = 1; $month <= 12; $month++) {
            $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
            $dateTo = Carbon::create($year, $month, 1)->endOfMonth();

            // Income for the month
            $income = Booking::where('hotel_id', $hotelId)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('total_price');

            // Expenses for the month
            $expenses = Expense::where('hotel_id', $hotelId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->sum('amount');

            $profit = $income - $expenses;

            $monthlyData[] = [
                'month' => $dateFrom->format('M'),
                'month_number' => $month,
                'month_name' => $dateFrom->locale('id')->translatedFormat('F'),
                'income' => (float) $income,
                'expenses' => (float) $expenses,
                'profit' => (float) $profit,
            ];
        }

        return response()->json(['data' => $monthlyData], 200);
    }

    /**
     * Get expense breakdown by category
     */
    public function expenseBreakdown(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();
        $year = $request->query('year', date('Y'));
        $month = $request->query('month');

        // Set date range
        if ($month) {
            $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
            $dateTo = Carbon::create($year, $month, 1)->endOfMonth();
        } else {
            $dateFrom = Carbon::create($year, 1, 1)->startOfYear();
            $dateTo = Carbon::create($year, 12, 31)->endOfYear();
        }

        $expenses = Expense::select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->where('hotel_id', $hotelId)
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->groupBy('category')
            ->get();

        $totalExpenses = $expenses->sum('total');

        $breakdown = $expenses->map(function ($expense) use ($totalExpenses) {
            $percentage = $totalExpenses > 0 ? ($expense->total / $totalExpenses) * 100 : 0;
            
            return [
                'category' => $expense->category,
                'amount' => (float) $expense->total,
                'count' => $expense->count,
                'percentage' => round($percentage, 2),
            ];
        })->sortByDesc('amount')->values();

        return response()->json([
            'data' => $breakdown,
            'total_expenses' => (float) $totalExpenses,
        ], 200);
    }

    /**
     * Get recent transactions (income and expenses)
     */
    public function recentTransactions(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();
        $limit = $request->query('limit', 50);
        $year = $request->query('year', date('Y'));

        // Get income transactions (bookings)
        $incomeTransactions = Booking::select(
            'id',
            'created_at as date',
            DB::raw("'income' as type"),
            DB::raw("'Booking' as category"),
            DB::raw("CONCAT('Pembayaran booking - ', guest_name, ' (Booking #BK-', id, ')') as description"),
            'total_price as amount'
        )
            ->where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereYear('created_at', $year);

        // Get expense transactions
        $expenseTransactions = Expense::select(
            'id',
            'date',
            DB::raw("'expense' as type"),
            'category',
            'description',
            'amount'
        )
            ->where('hotel_id', $hotelId)
            ->whereYear('date', $year);

        // Combine and sort by date
        $transactions = $incomeTransactions
            ->union($expenseTransactions)
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();

        $formattedTransactions = $transactions->map(function ($transaction) {
            return [
                'id' => $transaction->id,
                'date' => Carbon::parse($transaction->date)->format('Y-m-d'),
                'type' => $transaction->type,
                'category' => $transaction->category,
                'description' => $transaction->description,
                'amount' => (float) $transaction->amount,
            ];
        });

        return response()->json(['data' => $formattedTransactions], 200);
    }

    /**
     * Get income performance metrics
     */
    public function incomePerformance(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();
        $year = $request->query('year', date('Y'));

        // Monthly income data
        $monthlyIncome = [];
        for ($month = 1; $month <= 12; $month++) {
            $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
            $dateTo = Carbon::create($year, $month, 1)->endOfMonth();

            $income = Booking::where('hotel_id', $hotelId)
                ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
                ->whereBetween('created_at', [$dateFrom, $dateTo])
                ->sum('total_price');

            $monthlyIncome[$month] = (float) $income;
        }

        // Calculate metrics
        $totalIncome = array_sum($monthlyIncome);
        $averagePerMonth = $totalIncome / 12;
        $bestMonth = array_search(max($monthlyIncome), $monthlyIncome);
        $worstMonth = array_search(min($monthlyIncome), $monthlyIncome);

        $totalBookings = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereYear('created_at', $year)
            ->count();

        return response()->json([
            'data' => [
                'average_per_month' => round($averagePerMonth, 2),
                'best_month' => [
                    'month' => Carbon::create($year, $bestMonth, 1)->locale('id')->translatedFormat('F'),
                    'month_number' => $bestMonth,
                    'amount' => $monthlyIncome[$bestMonth],
                ],
                'worst_month' => [
                    'month' => Carbon::create($year, $worstMonth, 1)->locale('id')->translatedFormat('F'),
                    'month_number' => $worstMonth,
                    'amount' => $monthlyIncome[$worstMonth],
                ],
                'total_bookings' => $totalBookings,
                'total_income' => $totalIncome,
            ],
        ], 200);
    }

    /**
     * Get expense performance metrics
     */
    public function expensePerformance(Request $request)
    {
        $hotelId = $this->getOwnerHotelId();
        $year = $request->query('year', date('Y'));

        // Monthly expense data
        $monthlyExpenses = [];
        for ($month = 1; $month <= 12; $month++) {
            $dateFrom = Carbon::create($year, $month, 1)->startOfMonth();
            $dateTo = Carbon::create($year, $month, 1)->endOfMonth();

            $expenses = Expense::where('hotel_id', $hotelId)
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->sum('amount');

            $monthlyExpenses[$month] = (float) $expenses;
        }

        // Calculate metrics
        $totalExpenses = array_sum($monthlyExpenses);
        $averagePerMonth = $totalExpenses / 12;
        $highestMonth = array_search(max($monthlyExpenses), $monthlyExpenses);
        $lowestMonth = array_search(min($monthlyExpenses), $monthlyExpenses);

        // Determine efficiency status
        $efficiency = $averagePerMonth < 45000000 ? 'Baik' : ($averagePerMonth < 60000000 ? 'Sedang' : 'Perlu Optimasi');

        return response()->json([
            'data' => [
                'average_per_month' => round($averagePerMonth, 2),
                'highest_month' => [
                    'month' => Carbon::create($year, $highestMonth, 1)->locale('id')->translatedFormat('F'),
                    'month_number' => $highestMonth,
                    'amount' => $monthlyExpenses[$highestMonth],
                ],
                'lowest_month' => [
                    'month' => Carbon::create($year, $lowestMonth, 1)->locale('id')->translatedFormat('F'),
                    'month_number' => $lowestMonth,
                    'amount' => $monthlyExpenses[$lowestMonth],
                ],
                'efficiency' => $efficiency,
                'total_expenses' => $totalExpenses,
            ],
        ], 200);
    }

    /**
     * Ringkasan laporan keuangan owner (Legacy - untuk backward compatibility)
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
     * Laporan berdasarkan rentang tanggal (Legacy - untuk backward compatibility)
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
     * Daftar semua booking milik owner (Legacy - untuk backward compatibility)
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
