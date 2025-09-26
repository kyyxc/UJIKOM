<?php

use App\Http\Controllers\Api\Amenity\AmenityController;
use App\Http\Controllers\Api\Booking\BookingController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\Room\RoomController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Receptionist\ReceptionistBookingController;
use App\Http\Controllers\Receptionist\ReceptionistRoomController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'auth'], function () {
    Route::post('/signup', [App\Http\Controllers\Api\Auth\SignupController::class, 'signup']);
    Route::post('/signin', [App\Http\Controllers\Api\Auth\SessionController::class, 'signin']);
    Route::post('/signout', [App\Http\Controllers\Api\Auth\SessionController::class, 'signout'])->middleware('auth:sanctum');
});


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

Route::get('/amenities', [AmenityController::class, 'index']);

Route::post('/bookings/{id}/pay', [PaymentController::class, 'create']);
Route::post('/payments/callback', [PaymentController::class, 'callback']);

Route::get('/bookings', [BookingController::class, 'index']);
Route::get('/bookings/{booking:id}', [BookingController::class, 'show'])->missing(function () {
    return response()->json(['message' => 'Booking tidak ditemukan'], 404);
});

// Receptionist
Route::middleware(['auth:sanctum', 'api'])->prefix('receptionist')->group(function () {
    Route::get('/rooms', [ReceptionistRoomController::class, 'index']);
    Route::get('/rooms/{id}', [ReceptionistRoomController::class, 'show']);

    Route::get('/bookings', [ReceptionistBookingController::class, 'index']);
    Route::post('/bookings', [ReceptionistBookingController::class, 'store']);
    Route::post('/bookings/{id}/check-in', [ReceptionistBookingController::class, 'checkIn'])->missing(function () {
        return response()->json(['message' => 'Booking tidak ditemukan'], 404);
    });
    Route::post('/bookings/{id}/check-out', [ReceptionistBookingController::class, 'checkOut'])->missing(function () {
        return response()->json(['message' => 'Booking tidak ditemukan'], 404);
    });
});
