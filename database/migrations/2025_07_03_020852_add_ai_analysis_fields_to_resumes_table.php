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
        Schema::table('resume', function (Blueprint $table) {
            $table->string('ai_analysis_status')
              ->default('pending') // 'pending|processing|completed|failed'
              ->after('extracted_text');
              
        $table->unsignedTinyInteger('ai_progress')
              ->default(0)
              ->after('ai_analysis_status');
              
        $table->json('ai_results')
              ->nullable()
              ->after('ai_progress');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resume', function (Blueprint $table) {
            $table->dropColumn('ai_analysis_status');
            $table->dropColumn('ai_progress');
            $table->dropColumn('ai_results');
        });
    }
};