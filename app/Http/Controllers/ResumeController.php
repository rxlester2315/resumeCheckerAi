<?php

namespace App\Http\Controllers;

use App\Services\PdfParserService;
use Illuminate\Http\Request;
use App\Models\Resume;
use Illuminate\Support\Facades\Storage;

class ResumeController extends Controller
{

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

        $resume = Resume::create([
            'user_id' => auth()->id(),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $filePath,
            'file_type' => $file->extension(),
            'file_size' => $file->getSize(),
            'extracted_text' => $extractedText,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resume uploaded successfully',
            'original_name' => $file->getClientOriginalName(), // Match frontend
            'file_type' => $file->extension(), // Match frontend
            'file_size' => $file->getSize(), // Match frontend
            'extracted_text' => $extractedText,
            'resume_id' => $resume->id // Additional field
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
}