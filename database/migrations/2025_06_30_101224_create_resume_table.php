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
        Schema::create('resume', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('original_name'); // e.g., "john_doe_resume.pdf"
            $table->string('storage_path'); // e.g., "resumes/2023-11-15/abc123.pdf"
            $table->string('file_type'); // 'pdf' or 'docx'
            $table->unsignedInteger('file_size'); // in bytes
            $table->text('extracted_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resume');
    }
};