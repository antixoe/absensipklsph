<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->date('entry_date');
            $table->string('title');
            $table->text('description');
            $table->text('achievements')->nullable();
            $table->text('challenges')->nullable();
            $table->text('learning_outcomes')->nullable();
            $table->integer('hours_worked')->default(8);
            $table->string('status')->default('draft'); // draft, submitted, approved, rejected
            $table->foreignId('instructor_id')->nullable()->constrained('instructors')->onDelete('set null');
            $table->text('instructor_feedback')->nullable();
            $table->date('approved_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_entries');
    }
};
