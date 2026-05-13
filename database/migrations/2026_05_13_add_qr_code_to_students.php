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
        Schema::table('students', function (Blueprint $table) {
            // Add QR code reference for this student
            $table->foreignId('qr_code_id')->nullable()->constrained('q_r_codes')->onDelete('set null')->after('user_id');
            // Store the QR code string directly for quick access
            $table->string('student_qr_code')->nullable()->unique()->after('qr_code_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['qr_code_id']);
            $table->dropColumn(['qr_code_id', 'student_qr_code']);
        });
    }
};
