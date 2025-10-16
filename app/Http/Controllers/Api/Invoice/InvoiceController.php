<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['booking.hotel', 'booking.room', 'payment'])->paginate(10);

        return response()->json($invoices);
    }

    public function show($id)
    {
        try {
            // Ambil invoice beserta relasi booking, hotel, room, payment
            $invoice = Invoice::with([
                'booking.hotel',
                'booking.room',
                'booking.room.images',
                'payment'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $invoice
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
