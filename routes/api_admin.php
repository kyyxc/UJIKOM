<?php

use App\Http\Controllers\Api\Admin\AdminBookingController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminHotelController;
use App\Http\Controllers\Api\Admin\AdminUserController;
use App\Http\Controllers\Api\Admin\OwnerApprovalController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Room\AdminRoomController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    // Admin for manage hotels
    Route::apiResource('hotels', AdminHotelController::class)->missing(function ($request) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Hotel not found',
        ], 404);
    });

    // Admin for manage rooms
    Route::apiResource('rooms', AdminRoomController::class)->missing(function ($request) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Room not found',
        ], 404);
    });

    // Admin for get dashboard information
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [AdminDashboardController::class, 'getDashboardStats']);
        Route::get('/chart-data', [AdminDashboardController::class, 'getReservationsRevenueChart']);
        Route::get('/recent-reservations', [AdminDashboardController::class, 'getRecentReservations']);
        Route::get('/hotel-performance', [AdminDashboardController::class, 'getHotelPerformance']);
        Route::get('/status-distribution', [AdminDashboardController::class, 'getBookingStatusDistribution']);
        Route::get('/payment-methods', [AdminDashboardController::class, 'getPaymentMethodsSummary']);
        Route::get('/quick-stats', [AdminDashboardController::class, 'getQuickStats']);
        Route::get('/all', [AdminDashboardController::class, 'getAllDashboardData']);
    });

    Route::apiResource('bookings', AdminBookingController::class)->missing(function () {
        return response()->json([
            'status'  => 'error',
            'message' => 'Booking not found',
        ], 404);
    });


    // User management routes
    Route::prefix('users')->group(function () {
        Route::get('/statistics', [AdminUserController::class, 'statistics']);
        Route::post('/bulk-action', [AdminUserController::class, 'bulkAction']);
    });

    Route::apiResource('users', AdminUserController::class);

    // Owner Registration Management Routes
    Route::prefix('owner-registrations')->group(function () {
        // Statistics & Analysis
        Route::get('/statistics', [OwnerApprovalController::class, 'statistics']);
        Route::get('/waiting-analysis', [OwnerApprovalController::class, 'waitingAnalysis']);
        
        // Lists & Search
        Route::get('/', [OwnerApprovalController::class, 'index']);
        Route::get('/recent', [OwnerApprovalController::class, 'recent']);
        Route::get('/search', [OwnerApprovalController::class, 'search']);
        Route::get('/history', [OwnerApprovalController::class, 'history']);
        
        // Bulk Actions
        Route::post('/bulk-approve', [OwnerApprovalController::class, 'bulkApprove']);
        Route::post('/bulk-reject', [OwnerApprovalController::class, 'bulkReject']);
        
        // Detail & Actions
        Route::get('/{id}', [OwnerApprovalController::class, 'show']);
        Route::post('/{id}/approve', [OwnerApprovalController::class, 'approve']);
        Route::post('/{id}/reject', [OwnerApprovalController::class, 'reject']);
        Route::get('/{id}/download/{document_type}', [OwnerApprovalController::class, 'downloadDocument']);
    });
});
