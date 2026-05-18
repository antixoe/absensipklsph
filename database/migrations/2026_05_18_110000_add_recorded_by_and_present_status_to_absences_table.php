<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            if (!Schema::hasColumn('absences', 'recorded_by')) {
                $table->foreignId('recorded_by')
                    ->nullable()
                    ->after('status')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        if (Schema::hasColumn('absences', 'status') && DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE absences MODIFY status ENUM('pending', 'approved', 'rejected', 'present') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' && Schema::hasColumn('absences', 'status')) {
            DB::statement("ALTER TABLE absences MODIFY status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('absences', function (Blueprint $table) {
            if (Schema::hasColumn('absences', 'recorded_by')) {
                $table->dropForeign(['recorded_by']);
                $table->dropColumn('recorded_by');
            }
        });
    }
};
