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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Booking::class, 'booking_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(App\Models\Payment::class, 'payment_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            // Nomor invoice unik
            $table->string('invoice_number')->unique();

            // Nominal invoice (biasanya sama dengan total_price booking / payment->amount)
            $table->decimal('amount', 12, 2);

            // Tanggal invoice diterbitkan
            $table->date('invoice_date')->default(now());

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
