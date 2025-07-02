<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HuggingFaceService
{
    protected $client;
    protected $apiToken;
    protected $apiUrl = 'https://api-inference.huggingface.co/models/';

    // Models we'll use for different tasks
    protected $models = [
        'skills' => 'bert-base-uncased',
        'experience' => 'dslim/bert-base-NER', // Named Entity Recognition
        'quality' => 'distilbert-base-uncased',
    ];

    public function __construct()
    {
        $this->client = new Client();
        $this->apiToken = env('HUGGING_FACE_API_KEY');
    }

    /**
     * Extract skills from resume text
     */
    public function extractSkills(string $text): array
    {
        // First try to find a skills section
        if (preg_match('/SKILLS(.+?)(?=EXPERIENCE|EDUCATION|$)/is', $text, $matches)) {
            $skillsText = $matches[1];
        } else {
            $skillsText = $text;
        }

        $response = $this->callModel(
            $this->models['skills'],
            "Extract technical skills from this text: " . $skillsText
        );

        return $this->processSkillsResponse($response);
    }

    /**
     * Analyze work experience
     */
    public function analyzeExperience(string $text): array
    {
        $response = $this->callModel(
            $this->models['experience'],
            $text
        );

        return $this->processExperienceResponse($response);
    }

    /**
     * Analyze education section
     */
    public function analyzeEducation(string $text): array
    {
        // Try to isolate education section
        if (preg_match('/EDUCATION(.+?)(?=EXPERIENCE|SKILLS|$)/is', $text, $matches)) {
            $eduText = $matches[1];
        } else {
            $eduText = $text;
        }

        $response = $this->callModel(
            $this->models['experience'], // Using same NER model
            $eduText
        );

        return $this->processEducationResponse($response);
    }

    /**
     * Evaluate resume quality
     */
    public function evaluateQuality(string $text): float
    {
        $prompt = "Rate the quality of this resume text from 1-10: " . substr($text, 0, 1000);
        
        $response = $this->callModel(
            $this->models['quality'],
            $prompt
        );

        return $this->processQualityResponse($response);
    }

    /**
     * Generate recommendations
     */
    public function generateRecommendations(string $text): array
    {
        $prompt = "Provide 3 recommendations to improve this resume: " . substr($text, 0, 1000);
        
        $response = $this->callModel(
            $this->models['quality'],
            $prompt
        );

        return $this->processRecommendationsResponse($response);
    }

    /**
     * Generic model calling method
     */
    protected function callModel(string $model, string $input): array
    {
        try {
            $response = $this->client->post($this->apiUrl . $model, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => ['inputs' => $input],
                'timeout' => 30
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            \Log::error("Hugging Face API call failed: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    // ... (Add the various processResponse methods below)
    protected function processSkillsResponse(array $response): array { /* ... */ }
    protected function processExperienceResponse(array $response): array { /* ... */ }
    protected function processEducationResponse(array $response): array { /* ... */ }
    protected function processQualityResponse(array $response): float { /* ... */ }
    protected function processRecommendationsResponse(array $response): array { /* ... */ }
}