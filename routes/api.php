<?php

use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Amenity\AmenityController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\Invoice\InvoiceController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Receptionist\ReceptionistBookingController;
use App\Http\Controllers\Api\Receptionist\ReceptionistDashboardController;
use App\Http\Controllers\Api\Receptionist\ReceptionistGuestController;
use App\Http\Controllers\Api\Receptionist\ReceptionistRoomController;
use App\Http\Controllers\Api\Room\RoomController;
use App\Http\Controllers\Api\Room\RoomBookingDateController;
use App\Http\Controllers\Api\UserProfileController;

use App\Models\Payment;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Authentication
Route::group(['prefix' => 'auth'], function () {
    Route::post('/signup', [App\Http\Controllers\Api\Auth\SignupController::class, 'signup']);
    Route::post('/signin', [App\Http\Controllers\Api\Auth\SessionController::class, 'signin']);
    Route::post('/signout', [App\Http\Controllers\Api\Auth\SessionController::class, 'signout'])->middleware('auth:sanctum');
});

// User Profile (accessible by all authenticated users)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user/profile', [UserProfileController::class, 'show']);
    Route::post('/user/profile', [UserProfileController::class, 'update']);
    Route::delete('/user/profile/picture', [UserProfileController::class, 'deleteProfilePicture']);
    Route::post('/user/change-password', [UserProfileController::class, 'changePassword']);
});

// Role user
Route::middleware(['auth:sanctum', 'customer'])->group(function () {
    // User get hotels
    Route::apiResource('hotels', HotelController::class)->missing(function () {
        return response()->json([
            'status'  => 'error',
            'message' => 'Hotel not found',
        ], 404);
    })->withoutMiddleware(['auth:sanctum', 'customer']);

    // Get rooms by hotel
    Route::get('hotels/{id}/rooms', [HotelController::class, 'getRooms']);

    // User get rooms
    Route::apiResource('rooms', RoomController::class)->missing(function () {
        return response()->json([
            'status'  => 'error',
            'message' => 'Room not found',
        ], 404);
    });

    // Get room booked dates
    Route::get('rooms/{id}/booked-dates', [RoomBookingDateController::class, 'getBookedDates']);

    // User booking hotels
    Route::apiResource('bookings', BookingController::class)->missing(function () {
        return response()->json([
            'status'  => 'error',
            'message' => 'Booking not found',
        ], 404);
    });

    // User booking hotel
    Route::post('/bookings/{id}/pay', [PaymentController::class, 'create']);
    Route::post('/payments/callback', [PaymentController::class, 'callback']);

    // User get invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
});


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
    });

    return response()->json([
        'message' => 'Payment forced to success',
        'payment' => $payment->load('booking.room'),
    ]);
});

// Amenity for hotels and rooms
Route::get('/amenities', [AmenityController::class, 'index']);

// Countries endpoint (frontend country selector)
Route::get('/countries', [App\Http\Controllers\Api\CountryController::class, 'index']);


require __DIR__ . '/api_admin.php';
require __DIR__ . '/api_owner.php';
require __DIR__ . '/api_receptionist.php';
