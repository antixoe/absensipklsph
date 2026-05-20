<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rombongan_belajars', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Learning group / rombel name');
            $table->string('code')->nullable()->unique()->comment('Short class code');
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rombel_id')
                ->nullable()
                ->after('level')
                ->constrained('rombongan_belajars')
                ->nullOnDelete();
        });

        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('rombel_id')
                ->nullable()
                ->after('internship_program_id')
                ->constrained('rombongan_belajars')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rombel_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rombel_id');
        });

        Schema::dropIfExists('rombongan_belajars');
    }
};
