<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * ✅ Create Payment & Snap Token
     */
    public function create(Request $request, $bookingId)
    {
        $booking = Booking::with(['room.hotel', 'guest'])->findOrFail($bookingId);

        // cek payment yang masih pending
        $payment = $booking->payment()->where('status', 'pending')->latest()->first();

        if (!$payment) {
            // kalau belum ada pending payment → buat baru
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->total_price,
                'status' => 'pending',
                'midtrans_order_id' => 'ORDER-' . uniqid(),
            ]);
        }

        // hitung nights
        $nights = $booking->check_in_date && $booking->check_out_date
            ? Carbon::parse($booking->check_in_date)->diffInDays(Carbon::parse($booking->check_out_date))
            : 1;

        $params = [
            'transaction_details' => [
                'order_id' => $payment->midtrans_order_id,
                'gross_amount' => $booking->total_price,
            ],
            'customer_details' => [
                'first_name' => $booking->guest->full_name,
                'email' => $booking->guest->email,
                'phone' => $booking->guest->phone_number,
            ],
            'item_details' => [[
                'id' => $booking->room->id,
                'price' => $booking->room->price_per_night,
                'quantity' => $nights,
                'name' => $booking->room->room_type . ' Room',
            ]],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($params);

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
        // Validasi signature key biar aman
        $expectedSignature = hash(
            'sha512',
            $request->order_id .
                $request->status_code .
                $request->gross_amount .
                Config::$serverKey
        );

        if ($request->signature_key !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $notification = new Notification();
        $orderId = $notification->order_id;
        $transactionStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;
        $fraudStatus = $notification->fraud_status ?? null;

        $payment = Payment::where('midtrans_order_id', $orderId)->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Update status payment
        $status = match ($transactionStatus) {
            'settlement' => 'paid',
            'capture' => ($fraudStatus == 'challenge') ? 'pending' : 'paid',
            'pending' => 'pending',
            'deny', 'cancel' => 'cancelled',
            'expire' => 'expired',
            default => 'failed',
        };

        $payment->update([
            'midtrans_transaction_id' => $notification->transaction_id,
            'midtrans_payment_type' => $paymentType,
            'midtrans_response' => json_encode($notification),
            'transaction_date' => now(),
            'status' => $status,
        ]);

        if ($status === 'paid') {
            $payment->booking()->update(['status' => 'confirmed']);
        }

        return response()->json(['message' => 'Payment status updated']);
    }
}
