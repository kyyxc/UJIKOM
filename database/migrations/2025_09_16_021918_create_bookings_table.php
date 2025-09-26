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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // booking online (user_id) - nullable biar tidak wajib saat offline booking
            $table->foreignIdFor(App\Models\User::class, 'user_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            // booking offline (receptionist_id)
            $table->foreignId('receptionist_id')
                ->nullable()
                ->constrained('receptionists')
                ->cascadeOnDelete();

            $table->foreignIdFor(App\Models\Room::class, 'room_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignIdFor(App\Models\Hotel::class, 'hotel_id')
                ->constrained()
                ->cascadeOnDelete();

            // data tamu (penting utk booking offline)
            $table->string('guest_name');
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();

            $table->date('check_in_date');
            $table->date('check_out_date');

            $table->enum('status', [
                'pending',    // booking online menunggu pembayaran
                'confirmed',  // booking online sudah dibayar
                'booked',     // booking offline langsung dibooking
                'checked_in',
                'checked_out',
                'cancelled'
            ])->default('pending');

            $table->enum('source', ['online', 'offline'])->default('online');

            $table->decimal('total_price', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
