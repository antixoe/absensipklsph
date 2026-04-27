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
            // Add approval tracking for each party
            $table->boolean('student_approved')->default(false)->after('completion_status');
            $table->timestamp('student_approved_at')->nullable()->after('student_approved');
            
            $table->boolean('company_mentor_approved')->default(false)->after('student_approved_at');
            $table->timestamp('company_mentor_approved_at')->nullable()->after('company_mentor_approved');
            
            $table->boolean('school_teacher_approved')->default(false)->after('company_mentor_approved_at');
            $table->timestamp('school_teacher_approved_at')->nullable()->after('school_teacher_approved');
            
            // Drop old signature columns
            $table->dropColumn(['student_signature_path', 'company_mentor_signature_path', 'school_teacher_signature_path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_agendas', function (Blueprint $table) {
            $table->string('student_signature_path')->nullable();
            $table->string('company_mentor_signature_path')->nullable();
            $table->string('school_teacher_signature_path')->nullable();
            
            $table->dropColumn([
                'student_approved',
                'student_approved_at',
                'company_mentor_approved',
                'company_mentor_approved_at',
                'school_teacher_approved',
                'school_teacher_approved_at'
            ]);
        });
    }
};
