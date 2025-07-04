<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HuggingFaceService
{
    protected $client;
    protected $apiToken;
    protected $apiUrl = 'https://api-inference.huggingface.co/models/';

    protected $models = [
        'skills' => 'bert-base-uncased',
        'experience' => 'dslim/bert-base-NER',
        'quality' => 'distilbert-base-uncased',
    ];

    public function __construct()
    {
        $this->client = new Client();
        $this->apiToken = env('HUGGING_FACE_API_KEY');
          // Add validation
    if (empty($this->apiToken)) {
        throw new \RuntimeException('Hugging Face API key not configured');
    }
    }

    public function extractSkills(string $text): array
    {
        if (preg_match('/SKILLS(.+?)(?=EXPERIENCE|EDUCATION|$)/is', $text, $matches)) {
            $skillsText = $matches[1];
        } else {
            $skillsText = $text;
        }

        $response = $this->callModel(
            $this->models['skills'],
            "Extract technical skills from: " . $skillsText
        );

        return $this->processSkillsResponse($response);
    }

    public function analyzeExperience(string $text): array
    {
        $response = $this->callModel(
            $this->models['experience'],
            $text
        );

        return $this->processExperienceResponse($response);
    }

    public function analyzeEducation(string $text): array
    {
        if (preg_match('/EDUCATION(.+?)(?=EXPERIENCE|SKILLS|$)/is', $text, $matches)) {
            $eduText = $matches[1];
        } else {
            $eduText = $text;
        }

        $response = $this->callModel(
            $this->models['experience'],
            $eduText
        );

        return $this->processEducationResponse($response);
    }

    public function evaluateQuality(string $text): float
    {
        $response = $this->callModel(
            $this->models['quality'],
            "Rate quality (1-10) of: " . substr($text, 0, 1000)
        );

        return $this->processQualityResponse($response);
    }

    public function generateRecommendations(string $text): array
    {
        $response = $this->callModel(
            $this->models['quality'],
            "Suggest 3 improvements for: " . substr($text, 0, 1000)
        );

        return $this->processRecommendationsResponse($response);
    }

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

        if ($response->getStatusCode() === 403) {
            throw new \Exception('Token missing inference permissions. Regenerate token with "Write" access.');
        }

        return json_decode($response->getBody()->getContents(), true) ?? [];

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        $response = $e->getResponse();
        $body = $response ? json_decode($response->getBody()->getContents(), true) : [];
        return ['error' => $body['error'] ?? $e->getMessage()];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

    protected function processSkillsResponse(array $response): array
    {
        if (isset($response['error'])) {
            return ['error' => $response['error']];
        }

        if (isset($response[0]['sequence'])) {
            return array_map('trim', explode(',', $response[0]['sequence']));
        }

        return [];
    }

    public function isModelReady(string $model): bool
{
    try {
        $response = $this->client->get($this->apiUrl . $model, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiToken]
        ]);
        
        $data = json_decode($response->getBody()->getContents(), true);
        return $data['loaded'] ?? false;
        
    } catch (\Exception $e) {
        return false;
    }
}

    protected function processExperienceResponse(array $response): array
    {
        if (isset($response['error'])) {
            return ['error' => $response['error']];
        }

        $experience = [];
        foreach ($response as $item) {
            if (isset($item['entity_group']) && in_array($item['entity_group'], ['ORG', 'TITLE'])) {
                $experience[] = [
                    'type' => $item['entity_group'],
                    'value' => $item['word'],
                    'score' => $item['score'] ?? 0
                ];
            }
        }

        return $experience ?: [];
    }

    protected function processEducationResponse(array $response): array
    {
        if (isset($response['error'])) {
            return ['error' => $response['error']];
        }

        $education = [];
        foreach ($response as $item) {
            if (isset($item['entity_group']) && $item['entity_group'] === 'EDU') {
                $education[] = [
                    'institution' => $item['word'],
                    'score' => $item['score'] ?? 0
                ];
            }
        }

        return $education ?: [];
    }

    protected function processQualityResponse(array $response): float
    {
        if (isset($response['error'])) {
            return 0.0;
        }

        if (isset($response[0]['score'])) {
            return min(10, max(1, round($response[0]['score'] * 10, 1)));
        }

        return 5.0;
    }

    protected function processRecommendationsResponse(array $response): array
    {
        if (isset($response['error'])) {
            return [$response['error']];
        }

        if (isset($response[0]['generated_text'])) {
            return array_filter(array_map('trim', 
                explode("\n", $response[0]['generated_text'])));
        }

        return [
            'Add more measurable achievements',
            'Include relevant technical keywords',
            'Showcase leadership experiences'
        ];
    }
}