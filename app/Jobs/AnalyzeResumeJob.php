<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Resume;
use App\Services\HuggingFaceService;

class AnalyzeResumeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Resume $resume)
    {
        //
    }

    /**
     * Execute the job.
     */


    public function handle(HuggingFaceService $huggingFace)
{
    $this->resume->update([
        'ai_analysis_status' => 'processing',
        'ai_progress' => 10
    ]);

    try {
        $analysis = [
            'skills' => $huggingFace->extractSkills($this->resume->extracted_text),
            'experience' => $huggingFace->analyzeExperience($this->resume->extracted_text),
            'education' => $huggingFace->analyzeEducation($this->resume->extracted_text),
            'quality_score' => $huggingFace->evaluateQuality($this->resume->extracted_text),
            'recommendations' => $huggingFace->generateRecommendations($this->resume->extracted_text)
        ];

        $this->resume->update([
            'ai_analysis_status' => 'completed',
            'ai_progress' => 100,
            'ai_results' => $analysis
        ]);

    } catch (\Exception $e) {
        $this->resume->update([
            'ai_analysis_status' => 'partial',
            'ai_results' => [
                'error' => $e->getMessage(),
                'partial_results' => true
            ]
        ]);
    }
}

}