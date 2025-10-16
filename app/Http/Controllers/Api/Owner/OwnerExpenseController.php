<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OwnerExpenseController extends Controller
{
    /**
     * Display a listing of expenses for owner's hotel.
     */
    public function index(Request $request)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $expenses = Expense::where('hotel_id', $owner->hotel_id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $expenses->map(fn($expense) => [
                'id' => $expense->id,
                'category' => $expense->category,
                'amount' => (float) $expense->amount,
                'description' => $expense->description,
                'date' => $expense->date->format('Y-m-d'),
                'payment_method' => $expense->payment_method,
                'receipt_number' => $expense->receipt_number,
                'created_at' => $expense->created_at->toISOString(),
            ]),
        ], 200);
    }

    /**
     * Store a newly created expense.
     */
    public function store(Request $request)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|string|in:Gaji,Operasional,Maintenance,Utilitas,Marketing,Supplies,Lain-lain',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'date' => 'required|date',
            'payment_method' => 'nullable|string|in:Cash,Transfer Bank,Credit Card,Debit Card',
            'receipt_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $expense = Expense::create([
            'hotel_id' => $owner->hotel_id,
            'category' => $request->category,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
        ]);

        return response()->json([
            'message' => 'Pengeluaran berhasil ditambahkan',
            'data' => [
                'id' => $expense->id,
                'category' => $expense->category,
                'amount' => (float) $expense->amount,
                'description' => $expense->description,
                'date' => $expense->date->format('Y-m-d'),
                'payment_method' => $expense->payment_method,
                'receipt_number' => $expense->receipt_number,
                'created_at' => $expense->created_at->toISOString(),
            ],
        ], 201);
    }

    /**
     * Display the specified expense.
     */
    public function show(Request $request, $id)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $expense = Expense::where('hotel_id', $owner->hotel_id)->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Pengeluaran tidak ditemukan'], 404);
        }

        return response()->json([
            'data' => [
                'id' => $expense->id,
                'category' => $expense->category,
                'amount' => (float) $expense->amount,
                'description' => $expense->description,
                'date' => $expense->date->format('Y-m-d'),
                'payment_method' => $expense->payment_method,
                'receipt_number' => $expense->receipt_number,
                'created_at' => $expense->created_at->toISOString(),
            ],
        ], 200);
    }

    /**
     * Update the specified expense.
     */
    public function update(Request $request, $id)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $expense = Expense::where('hotel_id', $owner->hotel_id)->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Pengeluaran tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'category' => 'required|string|in:Gaji,Operasional,Maintenance,Utilitas,Marketing,Supplies,Lain-lain',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:1000',
            'date' => 'required|date',
            'payment_method' => 'nullable|string|in:Cash,Transfer Bank,Credit Card,Debit Card',
            'receipt_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $expense->update([
            'category' => $request->category,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
        ]);

        return response()->json([
            'message' => 'Pengeluaran berhasil diperbarui',
            'data' => [
                'id' => $expense->id,
                'category' => $expense->category,
                'amount' => (float) $expense->amount,
                'description' => $expense->description,
                'date' => $expense->date->format('Y-m-d'),
                'payment_method' => $expense->payment_method,
                'receipt_number' => $expense->receipt_number,
                'created_at' => $expense->created_at->toISOString(),
            ],
        ], 200);
    }

    /**
     * Remove the specified expense.
     */
    public function destroy(Request $request, $id)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        $expense = Expense::where('hotel_id', $owner->hotel_id)->find($id);

        if (!$expense) {
            return response()->json(['message' => 'Pengeluaran tidak ditemukan'], 404);
        }

        $expense->delete();

        return response()->json([
            'message' => 'Pengeluaran berhasil dihapus',
        ], 200);
    }

    /**
     * Get expense statistics for owner's hotel.
     */
    public function statistics(Request $request)
    {
        $owner = $request->user()->owner;

        if (!$owner) {
            return response()->json(['message' => 'Anda bukan owner'], 403);
        }

        // Get filter parameters
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $category = $request->query('category');

        $query = Expense::where('hotel_id', $owner->hotel_id);

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $expenses = $query->get();

        $totalExpenses = $expenses->sum('amount');
        $totalTransactions = $expenses->count();
        $averagePerTransaction = $totalTransactions > 0 ? $totalExpenses / $totalTransactions : 0;

        // Group by category
        $byCategory = $expenses->groupBy('category')->map(function ($items, $category) {
            return [
                'category' => $category,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->values();

        // Group by month
        $byMonth = $expenses->groupBy(function ($expense) {
            return $expense->date->format('Y-m');
        })->map(function ($items, $month) {
            return [
                'month' => $month,
                'total' => $items->sum('amount'),
                'count' => $items->count(),
            ];
        })->values();

        return response()->json([
            'data' => [
                'total_expenses' => (float) $totalExpenses,
                'total_transactions' => $totalTransactions,
                'average_per_transaction' => (float) $averagePerTransaction,
                'by_category' => $byCategory,
                'by_month' => $byMonth,
            ],
        ], 200);
    }
}
