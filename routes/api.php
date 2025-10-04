<?php

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Amenity\AmenityController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\Invoice\InvoiceController;
use App\Http\Controllers\Api\Receptionist\ReceptionistBookingController;
use App\Http\Controllers\Api\Receptionist\ReceptionistDashboardController;
use App\Http\Controllers\Api\Receptionist\ReceptionistGuestController;
use App\Http\Controllers\Api\Receptionist\ReceptionistRoomController;
use App\Http\Controllers\Api\Room\RoomController;
use App\Http\Controllers\PaymentController;
use App\Models\Payment;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function () {
    Route::post('/signup', [App\Http\Controllers\Api\Auth\SignupController::class, 'signup']);
    Route::post('/signin', [App\Http\Controllers\Api\Auth\SessionController::class, 'signin']);
    Route::post('/signout', [App\Http\Controllers\Api\Auth\SessionController::class, 'signout'])->middleware('auth:sanctum');
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('hotels', HotelController::class)->missing(function ($request) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Hotel not found',
        ], 404);
    });

    Route::apiResource('rooms', RoomController::class)->missing(function ($request) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Room not found',
        ], 404);
    });

    Route::apiResource('bookings', BookingController::class)->missing(function ($request) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Booking not found',
        ], 404);
    });
    Route::post('/bookings/{id}/pay', [PaymentController::class, 'create']);
    Route::post('/payments/callback', [PaymentController::class, 'callback']);

    Route::get('/amenities', [AmenityController::class, 'index']);

    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
});


// Receptionist
Route::middleware(['auth:sanctum'])->prefix('receptionist')->group(function () {
    Route::get('/rooms', [ReceptionistRoomController::class, 'index']);
    Route::get('/rooms/{id}', [ReceptionistRoomController::class, 'show']);

    Route::get('/bookings', [ReceptionistBookingController::class, 'index']);
    Route::post('/bookings', [ReceptionistBookingController::class, 'booking']);
    Route::post('/payments', [ReceptionistBookingController::class, 'payment']);
    Route::post('/bookings/{id}/check-in', [ReceptionistBookingController::class, 'checkIn'])->missing(function () {
        return response()->json(['message' => 'Booking tidak ditemukan'], 404);
    });
    Route::post('/bookings/{booking}/check-out', [ReceptionistBookingController::class, 'checkOut'])->missing(function () {
        return response()->json(['message' => 'Booking tidak ditemukan'], 404);
    });

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
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/receptionist/guests', [ReceptionistGuestController::class, 'index']);
});

// Owner
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
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
});

// Test midtrans
use Illuminate\Support\Facades\DB;

Route::post('/payments/test-success/{payment}', function (Payment $payment) {
    DB::transaction(function () use ($payment) {
        // Update payment
        $payment->update([
            'status' => 'paid',
            'midtrans_transaction_id' => 'TEST-' . uniqid(),
            'midtrans_payment_type' => 'qris',
            'midtrans_response' => json_encode(['test' => true]),
            'transaction_date' => now(),
        ]);

        // Update booking status
        $booking = $payment->booking;
        $booking->update(['status' => 'confirmed']);

        // Update room status → occupied
        if ($booking->room) {
            $booking->room->update(['status' => 'occupied']);
        }
    });

    return response()->json([
        'message' => 'Payment forced to success',
        'payment' => $payment->load('booking.room'),
    ]);
});
