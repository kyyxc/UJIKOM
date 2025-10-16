<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Receptionist\{
    ReceptionistRoomController,
    ReceptionistBookingController,
    ReceptionistBookingStatusController,
    ReceptionistDashboardController,
    ReceptionistGuestController
};

Route::middleware(['auth:sanctum', 'receptionist'])->prefix('receptionist')->group(function () {
    // Receptionist manage rooms
    Route::prefix('rooms')->group(function () {
        Route::get('/', [ReceptionistRoomController::class, 'index']);
        Route::get('/{id}', [ReceptionistRoomController::class, 'show']);
    });

    // Receptionist manage bookings
    Route::prefix('bookings')->group(function () {
        Route::get('/', [ReceptionistBookingController::class, 'index']);
        Route::post('/', [ReceptionistBookingController::class, 'booking']);
        Route::post('/{id}/check-in', [ReceptionistBookingController::class, 'checkIn'])
            ->missing(fn() => response()->json(['message' => 'Booking tidak ditemukan'], 404));
        Route::post('/{booking}/check-out', [ReceptionistBookingController::class, 'checkOut'])
            ->missing(fn() => response()->json(['message' => 'Booking tidak ditemukan'], 404));
        Route::get('/checked-in', [ReceptionistBookingStatusController::class, 'checkedIn']);
        Route::get('/checked-out', [ReceptionistBookingStatusController::class, 'checkedOut']);
    });

    Route::post('/payments', [ReceptionistBookingController::class, 'payment']);



    // Receptionist get dashboard information
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [ReceptionistDashboardController::class, 'getDashboardStats']);
        Route::get('/room-status', [ReceptionistDashboardController::class, 'getRoomStatusData']);
        Route::get('/reservation-trends', [ReceptionistDashboardController::class, 'getReservationTrends']);
        Route::get('/payment-methods', [ReceptionistDashboardController::class, 'getPaymentMethodsData']);
        Route::get('/todays-activities', [ReceptionistDashboardController::class, 'getTodaysActivities']);
        Route::get('/monthly-revenue', [ReceptionistDashboardController::class, 'getMonthlyRevenue']);
        Route::get('/occupancy-rate', [ReceptionistDashboardController::class, 'getOccupancyRate']);
        Route::get('/all', [ReceptionistDashboardController::class, 'getAllDashboardData']);
    });

    // Receptionist get info guests
    Route::get('/guests', [ReceptionistGuestController::class, 'index']);
});
