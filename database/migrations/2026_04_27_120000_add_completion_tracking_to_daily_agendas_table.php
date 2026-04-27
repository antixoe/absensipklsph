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
        Schema::table('daily_agendas', function (Blueprint $table) {
            // Add fields to track completion by instructor/admin
            $table->boolean('is_completed')->default(false)->after('submitted_at');
            $table->foreignId('completed_by')->nullable()->after('is_completed')->constrained('users')->onDelete('set null');
            $table->timestamp('completed_at')->nullable()->after('completed_by');
            $table->text('instructor_notes')->nullable()->after('completed_at');
            $table->string('completion_status')->default('pending')->after('instructor_notes'); // pending, approved, rejected
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_agendas', function (Blueprint $table) {
            $table->dropForeign(['completed_by']);
            $table->dropColumn([
                'is_completed',
                'completed_by',
                'completed_at',
                'instructor_notes',
                'completion_status'
            ]);
        });
    }
};
