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
        Schema::table('activity_logs', function (Blueprint $table) {
            // Add soft delete support
            $table->softDeletes();
            
            // Add location tracking
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_city')->nullable();
            $table->string('location_country')->nullable();
            
            // Add device/browser information
            $table->string('device_type')->nullable(); // mobile, tablet, desktop
            $table->string('browser')->nullable();
            $table->string('operating_system')->nullable();
            
            // Add additional tracking
            $table->string('method')->nullable(); // GET, POST, PUT, DELETE, etc.
            $table->string('url_path')->nullable();
            $table->json('old_values')->nullable(); // For tracking data changes
            $table->json('new_values')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'latitude',
                'longitude',
                'location_name',
                'location_city',
                'location_country',
                'device_type',
                'browser',
                'operating_system',
                'method',
                'url_path',
                'old_values',
                'new_values',
            ]);
        });
    }
};
