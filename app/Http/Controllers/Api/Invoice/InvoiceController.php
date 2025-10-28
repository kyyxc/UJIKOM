<?php

namespace App\Http\Controllers\Api\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Get invoices for authenticated user only
        $user = $request->user();
        
        $invoices = Invoice::with(['booking.hotel', 'booking.room', 'payment'])
            ->whereHas('booking', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($invoices);
    }

    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            
            // Ambil invoice beserta relasi booking, hotel, room, payment
            // Only for authenticated user's invoices
            $invoice = Invoice::with([
                'booking.hotel',
                'booking.room',
                'booking.room.images',
                'payment'
            ])
            ->whereHas('booking', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->findOrFail($id);

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
