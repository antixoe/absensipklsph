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
        Schema::table('q_r_codes', function (Blueprint $table) {
            // Change qr_date from date to dateTime
            $table->dateTime('qr_date')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('q_r_codes', function (Blueprint $table) {
            // Revert back to date
            $table->date('qr_date')->change();
        });
    }
};
