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
            $table->dateTime('absence_date_time')->nullable();
        });

        // Copy existing date data to new datetime column
        DB::table('absences')
            ->select('id', 'absence_date')
            ->orderBy('id')
            ->chunkById(100, function ($absences) {
                foreach ($absences as $absence) {
                    DB::table('absences')
                        ->where('id', $absence->id)
                        ->update([
                            'absence_date_time' => $absence->absence_date
                                ? \Carbon\Carbon::parse($absence->absence_date)->startOfDay()
                                : null,
                        ]);
                }
            });

        // Drop the old column
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('absence_date');
        });

        // Rename the new column to the original name
        Schema::table('absences', function (Blueprint $table) {
            $table->renameColumn('absence_date_time', 'absence_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absences', function (Blueprint $table) {
            $table->date('absence_date_time')->nullable();
        });

        // Copy existing datetime data to date column
        DB::table('absences')
            ->select('id', 'absence_date')
            ->orderBy('id')
            ->chunkById(100, function ($absences) {
                foreach ($absences as $absence) {
                    DB::table('absences')
                        ->where('id', $absence->id)
                        ->update([
                            'absence_date_time' => $absence->absence_date
                                ? \Carbon\Carbon::parse($absence->absence_date)->toDateString()
                                : null,
                        ]);
                }
            });

        // Drop the old column
        Schema::table('absences', function (Blueprint $table) {
            $table->dropColumn('absence_date');
        });

        // Rename the new column back to the original name
        Schema::table('absences', function (Blueprint $table) {
            $table->renameColumn('absence_date_time', 'absence_date');
        });
    }
};
