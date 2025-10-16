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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained('hotels')->onDelete('cascade');
            $table->string('category'); // Gaji, Operasional, Maintenance, Utilitas, Marketing, Supplies, Lain-lain
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->date('date');
            $table->string('payment_method')->nullable(); // Cash, Transfer Bank, Credit Card, Debit Card
            $table->string('receipt_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
