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
        Schema::create('hotel_amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(App\Models\Hotel::class, 'hotel_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(App\Models\Amenity::class, 'amenity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_amenities');
    }
};
