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
        Schema::create('q_r_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique QR code identifier
            $table->date('qr_date'); // Date this QR code is for
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin who created
            $table->enum('status', ['active', 'expired', 'disabled'])->default('active');
            $table->text('notes')->nullable(); // Optional notes about this QR code
            $table->dateTime('expires_at')->nullable(); // When this QR code expires
            $table->timestamps();
            
            $table->index('qr_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('q_r_codes');
    }
};
