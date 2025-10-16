<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OwnerDashboardController extends Controller
{
    /**
     * Get comprehensive dashboard data for owner
     */
    public function index(Request $request)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $hotelId = $owner->hotel_id;

        // Get current month date range
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        // Get previous month date range for comparison
        $prevMonthStart = Carbon::now()->subMonth()->startOfMonth();
        $prevMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // === CURRENT MONTH DATA ===
        
        // Total income (current month)
        $totalIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_price');

        // Total bookings (current month)
        $totalBookings = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->count();

        // Total expenses (current month)
        $totalExpenses = Expense::where('hotel_id', $hotelId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->sum('amount');

        // Net profit
        $netProfit = $totalIncome - $totalExpenses;

        // === PREVIOUS MONTH DATA (for growth calculation) ===
        
        $prevIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$prevMonthStart, $prevMonthEnd])
            ->sum('total_price');

        $prevExpenses = Expense::where('hotel_id', $hotelId)
            ->whereBetween('date', [$prevMonthStart, $prevMonthEnd])
            ->sum('amount');

        $prevProfit = $prevIncome - $prevExpenses;

        // Calculate growth percentages
        $incomeGrowth = $prevIncome > 0 
            ? (($totalIncome - $prevIncome) / $prevIncome) * 100 
            : 0;

        $expenseGrowth = $prevExpenses > 0 
            ? (($totalExpenses - $prevExpenses) / $prevExpenses) * 100 
            : 0;

        $profitGrowth = $prevProfit > 0 
            ? (($netProfit - $prevProfit) / $prevProfit) * 100 
            : 0;

        // === RECENT EXPENSES (last 5) ===
        
        $recentExpenses = Expense::where('hotel_id', $hotelId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($expense) => [
                'id' => $expense->id,
                'category' => $expense->category,
                'amount' => (float) $expense->amount,
                'description' => $expense->description,
                'date' => $expense->date->format('Y-m-d'),
            ]);

        // === EXPENSE BREAKDOWN BY CATEGORY ===
        
        $expenseByCategory = Expense::select('category', DB::raw('SUM(amount) as total'))
            ->where('hotel_id', $hotelId)
            ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
            ->groupBy('category')
            ->get();

        $totalCategoryExpenses = $expenseByCategory->sum('total');

        $categoryBreakdown = $expenseByCategory->map(function ($item) use ($totalCategoryExpenses) {
            $percentage = $totalCategoryExpenses > 0 
                ? ($item->total / $totalCategoryExpenses) * 100 
                : 0;

            return [
                'category' => $item->category,
                'amount' => (float) $item->total,
                'percentage' => round($percentage, 1),
            ];
        })->sortByDesc('amount')->values();

        return response()->json([
            'summary' => [
                'total_income' => (float) $totalIncome,
                'total_expenses' => (float) $totalExpenses,
                'net_profit' => (float) $netProfit,
                'total_bookings' => $totalBookings,
                'income_growth' => round($incomeGrowth, 1),
                'expense_growth' => round($expenseGrowth, 1),
                'profit_growth' => round($profitGrowth, 1),
            ],
            'recent_expenses' => $recentExpenses,
            'category_breakdown' => $categoryBreakdown,
            'period' => [
                'current_month' => [
                    'start' => $currentMonthStart->format('Y-m-d'),
                    'end' => $currentMonthEnd->format('Y-m-d'),
                    'name' => $currentMonthStart->locale('id')->translatedFormat('F Y'),
                ],
                'previous_month' => [
                    'start' => $prevMonthStart->format('Y-m-d'),
                    'end' => $prevMonthEnd->format('Y-m-d'),
                    'name' => $prevMonthStart->locale('id')->translatedFormat('F Y'),
                ],
            ],
        ], 200);
    }

    /**
     * Get quick statistics for dashboard widgets
     */
    public function quickStats(Request $request)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $hotelId = $owner->hotel_id;

        // Today's stats
        $today = Carbon::today();
        
        $todayIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereDate('created_at', $today)
            ->sum('total_price');

        $todayBookings = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereDate('created_at', $today)
            ->count();

        $todayExpenses = Expense::where('hotel_id', $hotelId)
            ->whereDate('date', $today)
            ->sum('amount');

        // This week's stats
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $weekIncome = Booking::where('hotel_id', $hotelId)
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('total_price');

        $weekExpenses = Expense::where('hotel_id', $hotelId)
            ->whereBetween('date', [$weekStart, $weekEnd])
            ->sum('amount');

        return response()->json([
            'today' => [
                'income' => (float) $todayIncome,
                'bookings' => $todayBookings,
                'expenses' => (float) $todayExpenses,
                'profit' => (float) ($todayIncome - $todayExpenses),
            ],
            'this_week' => [
                'income' => (float) $weekIncome,
                'expenses' => (float) $weekExpenses,
                'profit' => (float) ($weekIncome - $weekExpenses),
            ],
        ], 200);
    }
}
