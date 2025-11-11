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
    Route::get('/dashboard', [ReceptionistDashboardController::class, 'getAllDashboardData']);

    // Receptionist get info guests
    Route::get('/guests', [ReceptionistGuestController::class, 'index']);
});
