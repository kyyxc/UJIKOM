<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function create(Request $request, $bookingId)
    {
        $booking = Booking::with('room.hotel')->findOrFail($bookingId);

        // Buat payment di DB dulu
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'status' => 'pending',
            'midtrans_order_id' => 'ORDER-' . uniqid(),
        ]);

        // Parameter ke Midtrans
        $params = [
            'transaction_details' => [
                'order_id' => $payment->midtrans_order_id,
                'gross_amount' => $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->user->full_name,
                'email' => $booking->user->email,
                'phone' => $booking->user->phone_number,
            ],
            'item_details' => [
                [
                    'id' => $booking->room->id,
                    'price' => $booking->room->price_per_night,
                    'quantity' => 1,
                    'name' => $booking->room->room_type . ' Room',
                ],
            ],
        ];

        // Buat Snap Token
        $snapToken = Snap::getSnapToken($params);

        return response()->json([
            'snap_token' => $snapToken,
            'payment' => $payment,
        ]);
    }

    /**
     * ✅ Callback Notification dari Midtrans
     */
    public function callback(Request $request)
    {
        $notification = new \Midtrans\Notification();

        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;
        $fraudStatus = $notification->fraud_status ?? null;

        $payment = Payment::where('midtrans_order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Update payment berdasarkan status Midtrans
        $payment->update([
            'midtrans_transaction_id' => $notification->transaction_id,
            'midtrans_payment_type' => $paymentType,
            'midtrans_response' => json_encode($notification),
            'transaction_date' => now(),
            'status' => match ($transactionStatus) {
                'settlement' => 'paid',
                'capture' => ($fraudStatus == 'challenge') ? 'pending' : 'paid',
                'pending' => 'pending',
                'deny', 'cancel' => 'cancelled',
                'expire' => 'expired',
                default => 'failed',
            }
        ]);

        return response()->json(['message' => 'Payment status updated']);
    }
}
