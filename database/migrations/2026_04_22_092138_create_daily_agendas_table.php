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
        Schema::create('daily_agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->date('agenda_date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->json('work_plan')->nullable(); // 5 rows
            $table->json('work_realization')->nullable(); // 5 rows
            $table->text('special_assignment')->nullable();
            $table->text('problems_found')->nullable();
            $table->json('daily_assessment')->nullable(); // 5 items with baik/kurang
            $table->text('notes')->nullable();
            $table->string('student_signature_path')->nullable();
            $table->string('company_mentor_signature_path')->nullable();
            $table->string('school_teacher_signature_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_agendas');
    }
};
