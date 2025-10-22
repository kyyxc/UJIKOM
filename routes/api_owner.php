<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Owner\OwnerExpenseController;
use App\Http\Controllers\Api\Owner\OwnerReportController;
use App\Http\Controllers\Api\Owner\OwnerDashboardController;
use App\Http\Controllers\Api\Owner\OwnerRegistrationController;
use App\Http\Controllers\Api\Owner\OwnerHotelController;
use App\Http\Controllers\Api\Owner\OwnerRoomController;

// Owner Registration Routes (public - no auth required for step 1)
Route::prefix('register')->group(function () {
    Route::post('/step-1', [OwnerRegistrationController::class, 'step1'])->withoutMiddleware(['auth:sanctum']); // Create account
    
    // Steps 2-5 require authentication
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/step-2', [OwnerRegistrationController::class, 'step2']); // Hotel basic info
        Route::post('/step-3', [OwnerRegistrationController::class, 'step3']); // Amenities & images
        Route::post('/step-4', [OwnerRegistrationController::class, 'step4']); // Banking & documents
        Route::post('/step-5', [OwnerRegistrationController::class, 'step5']); // Confirm & submit
        Route::get('/status', [OwnerRegistrationController::class, 'getStatus']); // Check status
    });
});

// Owner routes - require authentication with Sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Hotel Management Routes
    Route::prefix('hotel')->group(function () {
        Route::get('/', [OwnerHotelController::class, 'getHotelDetail']); // Get hotel detail with registration status
        Route::get('/registration-status', [OwnerHotelController::class, 'getRegistrationStatus']); // Get registration status only
        Route::get('/statistics', [OwnerHotelController::class, 'getHotelStatistics']); // Get hotel statistics
        Route::put('/', [OwnerHotelController::class, 'updateHotel']); // Update hotel info
        Route::post('/toggle-status', [OwnerHotelController::class, 'toggleHotelStatus']); // Toggle active status
    });
    
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
    
    // Room Management Routes
    Route::prefix('rooms')->group(function () {
        Route::get('/statistics', [OwnerRoomController::class, 'statistics']); // Get room statistics (must be before /{id})
        Route::get('/', [OwnerRoomController::class, 'index']); // Get all rooms
        Route::post('/', [OwnerRoomController::class, 'store']); // Create new room (with images & amenities)
        Route::get('/{id}', [OwnerRoomController::class, 'show']); // Get single room
        Route::put('/{id}', [OwnerRoomController::class, 'update']); // Update room (including images & amenities)
        Route::delete('/{id}', [OwnerRoomController::class, 'destroy']); // Delete room
        Route::patch('/{id}/status', [OwnerRoomController::class, 'updateStatus']); // Update room status
    });
    
});
