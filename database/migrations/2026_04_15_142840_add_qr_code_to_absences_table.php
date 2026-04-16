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
        Schema::table('absences', function (Blueprint $table) {
            $table->foreignId('qr_code_id')->nullable()->constrained('q_r_codes')->onDelete('set null');
            $table->dateTime('scanned_qr_at')->nullable(); // When QR code was scanned
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['qr_code_id']);
            $table->dropColumn(['qr_code_id', 'scanned_qr_at']);
        });
    }
};
