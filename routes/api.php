<?php

use App\Http\Controllers\Api\Amenity\AmenityController;
use App\Http\Controllers\Api\Hotel\HotelController;
use App\Http\Controllers\Api\Room\RoomController;
use App\Http\Controllers\PaymentController;
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
