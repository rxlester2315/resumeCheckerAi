<?php

namespace App\Http\Controllers;

use App\Services\PdfParserService;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{

        protected $huggingFace;


   public function __construct(HuggingFaceService $huggingFace)
    {
        $this->huggingFace = $huggingFace;
    }

public function upload(Request $request) 
{
    $request->validate([
        'resume' => 'required|file|mimes:pdf,docx|max:2048',
    ]);

    try {
        $file = $request->file('resume');
        $filePath = $file->store('resumes/' . date('Y-m-d'));
        
        $parser = new PdfParserService();
        $extractedText = $file->extension() === 'pdf'
            ? $parser->extractTextFromPdf(Storage::path($filePath))
            : $parser->extractTextFromDocx(Storage::path($filePath));

            $analysis = $this->analyzeResumeText($extractedText);

        $resume = Resume::create([
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $filePath,
            'file_type' => $file->extension(),
            'file_size' => $file->getSize(),
            'extracted_text' => $extractedText,
            'ai_analysis' => json_encode($analysis), // Store analysis results
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resume uploaded successfully',
            'original_name' => $file->getClientOriginalName(), // Match frontend
            'file_type' => $file->extension(), // Match frontend
            'file_size' => $file->getSize(), // Match frontend
            'extracted_text' => $extractedText,
            'resume_id' => $resume->id, // Additional field
            'ai_analysis' => $analysis // Send analysis to frontend
        ]);

    } catch (\Exception $e) {
        if (isset($filePath)) {
            Storage::delete($filePath);
        }
        return response()->json([
            'success' => false,
            'message' => 'Failed to store resume: ' . $e->getMessage(),
        ], 500);
    }
}



    /**
     * Example method to store the extracted text
     */
    protected function storeExtractedText(string $text): string
    {
        $fileName = 'resume_texts/' . uniqid() . '.txt';
        Storage::put($fileName, $text);
        return $fileName;
    }


 /**
     * Analyze resume text using Hugging Face AI
     */
    protected function analyzeResumeText(string $text): array
    {
        try {
            // Basic text cleaning
            $cleanedText = $this->cleanResumeText($text);
            
            // Get different types of analysis
            return [
                'skills' => $this->huggingFace->extractSkills($cleanedText),
                'experience' => $this->huggingFace->analyzeExperience($cleanedText),
                'education' => $this->huggingFace->analyzeEducation($cleanedText),
                'quality_score' => $this->huggingFace->evaluateQuality($cleanedText),
                'recommendations' => $this->huggingFace->generateRecommendations($cleanedText),
            ];
        } catch (\Exception $e) {
            // Log error but don't fail the whole upload
            \Log::error('AI analysis failed: ' . $e->getMessage());
            return ['error' => 'AI analysis partially failed'];
        }
    }

    /**
     * Clean resume text before analysis
     */
    protected function cleanResumeText(string $text): string
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove special characters except those that might be meaningful
        $text = preg_replace('/[^\w\s\-\.\,\:\;\@\/]/', '', $text);
        
        return trim($text);
    }





}