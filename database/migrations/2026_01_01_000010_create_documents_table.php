<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('document_name');
            $table->text('description')->nullable();
            $table->string('document_type'); // report, certificate, evidence, contract, etc
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->bigInteger('file_size');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->date('upload_date');
            $table->foreignId('reviewed_by')->nullable()->constrained('instructors')->onDelete('set null');
            $table->text('review_notes')->nullable();
            $table->date('review_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
