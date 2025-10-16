<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Owner\OwnerExpenseController;
use App\Http\Controllers\Api\Owner\OwnerReportController;
use App\Http\Controllers\Api\Owner\OwnerDashboardController;

// Owner routes - require authentication with Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Dashboard Routes
    Route::get('/dashboard', [OwnerDashboardController::class, 'index']);
    Route::get('/dashboard/quick-stats', [OwnerDashboardController::class, 'quickStats']);
    
    // Financial Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/financial-summary', [OwnerReportController::class, 'financialSummary']);
        Route::get('/monthly-trend', [OwnerReportController::class, 'monthlyTrend']);
        Route::get('/expense-breakdown', [OwnerReportController::class, 'expenseBreakdown']);
        Route::get('/transactions', [OwnerReportController::class, 'recentTransactions']);
        Route::get('/income-performance', [OwnerReportController::class, 'incomePerformance']);
        Route::get('/expense-performance', [OwnerReportController::class, 'expensePerformance']);
        
        // Legacy routes
        Route::get('/summary', [OwnerReportController::class, 'summary']);
        Route::get('/by-date', [OwnerReportController::class, 'reportByDate']);
        Route::get('/bookings', [OwnerReportController::class, 'bookings']);
    });
    
    // Expense Management Routes
    Route::prefix('expenses')->group(function () {
        Route::get('/', [OwnerExpenseController::class, 'index']); // Get all expenses
        Route::post('/', [OwnerExpenseController::class, 'store']); // Create new expense
        Route::get('/statistics', [OwnerExpenseController::class, 'statistics']); // Get statistics
        Route::get('/{id}', [OwnerExpenseController::class, 'show']); // Get single expense
        Route::put('/{id}', [OwnerExpenseController::class, 'update']); // Update expense
        Route::delete('/{id}', [OwnerExpenseController::class, 'destroy']); // Delete expense
    });
    
});
