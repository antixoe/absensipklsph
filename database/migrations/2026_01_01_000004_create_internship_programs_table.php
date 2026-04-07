<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('duration_weeks')->nullable();
            $table->string('status')->default('active'); // active, inactive, completed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_programs');
    }
};
