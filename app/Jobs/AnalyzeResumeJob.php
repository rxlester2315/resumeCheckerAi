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
        // Check if models are ready first
        foreach (['skills', 'experience', 'education'] as $modelType) {
            if (!$huggingFace->isModelReady($huggingFace->getModel($modelType))) {
                throw new \Exception("Model {$modelType} not ready");
            }
        }
        
        $analysis = $huggingFace->analyzeResumeText($this->resume->extracted_text);
        
        if (isset($analysis['error'])) {
            throw new \Exception($analysis['error']);
        }
        
        $this->resume->update([
            'ai_analysis_status' => 'completed',
            'ai_progress' => 100,
            'ai_results' => $analysis
        ]);
        
    } catch (\Exception $e) {
        $this->resume->update([
            'ai_analysis_status' => 'failed',
            'ai_results' => [
                'error' => $e->getMessage(),
                'retry_possible' => true,
                'failed_at' => now()->toDateTimeString()
            ]
        ]);
        
        // Retry after 5 minutes if it's a temporary error
        if (str_contains($e->getMessage(), ['timeout', 'loading', 'rate limit'])) {
            $this->release(300); // 5 minute delay
        }
    }
}
}