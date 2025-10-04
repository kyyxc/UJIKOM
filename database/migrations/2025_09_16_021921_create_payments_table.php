<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Booking::class, 'booking_id')->constrained()->cascadeOnDelete();

            // metode pembayaran (dari Midtrans)
            $table->string('payment_method')->nullable(); // contoh: "bank_transfer", "gopay", "qris"

            // jumlah yang dibayar
            $table->decimal('amount', 10, 2);

            // status sinkron dengan Midtrans
            $table->enum('status', [
                'pending',     // order dibuat, menunggu pembayaran
                'paid',        // sudah dibayar (settlement)
                'failed',      // gagal diproses
                'cancelled',   // dibatalkan
                'expired',     // kadaluarsa
                'refunded'     // dana dikembalikan
            ])->default('pending');

            $table->string('midtrans_order_id')->unique(); // order_id dari Midtrans
            $table->string('midtrans_transaction_id')->nullable(); // transaction_id dari Midtrans
            $table->string('midtrans_va_number')->nullable(); // kalau pakai VA bank transfer
            $table->string('midtrans_payment_type')->nullable(); // jenis pembayaran (gopay, qris, dll)
            $table->json('midtrans_response')->nullable(); // full response JSON (opsional untuk audit/debug)

            $table->timestamp('transaction_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
