<?php

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminHotelController;
use App\Http\Controllers\Api\Admin\AdminUserController;
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

    Route::apiResource('bookings', BookingController::class)->missing(function () {
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
});
