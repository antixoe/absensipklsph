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
        Schema::create('levels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Level name (e.g., Kesiswaan, Wali Kelas, Ketua Kelas, Sekretaris Kelas)');
            $table->text('description')->nullable()->comment('Level description');
            $table->enum('status', ['active', 'inactive'])->default('active')->comment('Level status');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
