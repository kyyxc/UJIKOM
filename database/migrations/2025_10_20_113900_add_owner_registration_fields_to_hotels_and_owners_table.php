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
        // Menambahkan field untuk data rekening dan dokumen legalitas di tabel owners
        Schema::table('owners', function (Blueprint $table) {
            // Data Rekening
            $table->string('bank_name')->nullable()->after('hotel_id');
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            
            // Dokumen Legalitas
            $table->string('business_license_number')->nullable(); // SIUP / NIB
            $table->string('business_license_file')->nullable(); // File SIUP / NIB
            $table->string('tax_id_number')->nullable(); // NPWP
            $table->string('tax_id_file')->nullable(); // File NPWP
            $table->string('identity_card_file')->nullable(); // KTP
            
            // Status registrasi
            $table->enum('registration_status', ['pending', 'step_1', 'step_2', 'step_3', 'step_4', 'completed', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
        });

        // Menambahkan field phone untuk hotel
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn([
                'bank_name',
                'account_number',
                'account_holder_name',
                'business_license_number',
                'business_license_file',
                'tax_id_number',
                'tax_id_file',
                'identity_card_file',
                'registration_status',
                'rejection_reason',
                'submitted_at',
                'approved_at',
            ]);
        });

        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
