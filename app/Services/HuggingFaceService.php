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
    'skills' => 'BAAI/bge-small-en-v1.5',  // Better for skills extraction
    'experience' => 'dslim/bert-base-NER', // Keep this working one
    'quality' => 'nickmuchi/deberta-v3-base-finetuned-finance-text-classification', // For quality scoring
    'recommendations' => 'google/flan-t5-base' // For text generati on
];

    public function verifyModels()
{
    $results = [];
    foreach ($this->models as $type => $model) {
        $results[$type] = $this->isModelAccessible($model);
    }
    return $results;
}

protected function isModelAccessible(string $model): bool
{
    try {
        $response = $this->client->head($this->apiUrl . $model, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiToken]
        ]);
        return $response->getStatusCode() === 200;
    } catch (\Exception $e) {
        return false;
    }
}


    // In your HuggingFaceService
protected $lastRequestTime = null;

protected function ensureRateLimit()
{
    if ($this->lastRequestTime && (microtime(true) - $this->lastRequestTime < 0.5)) {
        usleep(500000); // 500ms delay between requests
    }
    $this->lastRequestTime = microtime(true);
}

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
    // First try to find explicit skills section
    if (preg_match('/SKILLS[:]?(.+?)(?=EXPERIENCE|EDUCATION|$)/is', $text, $match)) {
        return $this->parseSkillsSection($match[1]);
    }
    
    // Fallback to whole-text analysis
    return $this->extractSkillsFromText($text);
}

protected function extractSkillsFromText(string $text): array
{
    // Match common tech terms
    preg_match_all('/\b(?:PHP|Laravel|Vue(?:\.js)?|React(?:\.js)?|JavaScript|Python|MySQL|Git|Node(?:\.js)?|Docker|AWS)\b/i', $text, $tech);
    
    // Match capitalized multi-word terms (for frameworks/tools)
    preg_match_all('/\b[A-Z][a-z]+(?: [A-Z][a-z]+)+\b/', $text, $frameworks);
    
    return array_unique(array_merge(
        array_map('strtolower', $tech[0]),
        $frameworks[0]
    ));

        
        
}


protected function parseSkillsSection(string $skillsText): array
{
    // Extract bullet points or comma-separated items
    preg_match_all('/(?:•|\d+\.|-)\s*([^\n]+)/', $skillsText, $matches);
    $skills = array_map('trim', $matches[1] ?? []);
    
    // Filter out non-skill items
    return array_filter($skills, function($skill) {
        return strlen($skill) > 3 && // Minimum length
               !preg_match('/years?|experience|proficient/i', $skill); // Filter descriptors
    });
}




protected function enhancedLocalSkillsExtraction(string $text): array
{
    // Match skills section if exists
    if (preg_match('/SKILLS[:]?(.+?)(?=EXPERIENCE|EDUCATION|$)/is', $text, $match)) {
        $skillsText = $match[1];
        
        // Extract bullet points or comma-separated skills
        preg_match_all('/(?:•|\d+\.)\s*(.+?)(?=\n|$)/', $skillsText, $bullets);
        preg_match_all('/\b(?:[A-Z][a-z]+(?: [A-Z][a-z]+)*)\b/', $skillsText, $words);
        
        return array_unique(array_merge(
            $bullets[1] ?? [],
            $words[0] ?? []
        ));
    }
    
    // Fallback: look for common tech terms
    preg_match_all('/\b(?:PHP|Laravel|Vue|JavaScript|Python|MySQL|Git|React|Node\.?js)\b/i', $text, $tech);
    return array_unique($tech[0] ?? []);
}



    

 public function analyzeExperience(string $text): array
{
    $apiResponse = $this->callModel(
        $this->models['experience'],
        "Extract jobs with titles, companies and durations: " . substr($text, 0, 2000)
    );
    
    if (isset($apiResponse['error'])) {
        return $this->parseExperienceFallback($text);
    }
    
    return $apiResponse;
}

protected function parseExperienceFallback(string $text): array
{
    $experience = [];
    $pattern = '/([A-Z][a-z]+ \d{4}|Present)\s*-\s*([A-Z][a-z]+ \d{4}|Present).+?at (.+?)(?=\n|$)/s';
    
    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $experience[] = [
                'position' => $this->extractPosition($match[0]),
                'company' => trim($match[3]),
                'duration' => trim($match[1] . ' to ' . $match[2])
            ];
        }
    }
    
    return $experience;
}

public function analyzeEducation(string $text): array
{
    $result = [
        'institutions' => [],
        'degrees' => [],
        'dates' => [],
        'full_entries' => []
    ];

    if (preg_match('/EDUCATION\s*[:]?(.+?)(?=EXPERIENCE|WORK|SKILLS|$)/is', $text, $match)) {
        $eduText = trim($match[1]);
        
        // Handle your specific format: "STI San Jose Del Monte Bachelor of Science in Information Technology 2024-2025"
        if (preg_match('/^([A-Z].+?)\s+(Bachelor|Master|B\.S\.|B\.A\.|M\.S\.|M\.A\.|Ph\.D\.).+?(\d{4}-\d{4})$/i', $eduText, $eduMatch)) {
            $institution = trim($eduMatch[1]);
            $degreeStart = strlen($institution);
            $datePos = strpos($eduText, $eduMatch[3]);

            if ($datePos !== false) {
                $degree = trim(substr($eduText, $degreeStart, $datePos - $degreeStart));
            } else {
                $degree = '';  // Or handle this case appropriately.
            }

            $dates = trim($eduMatch[3]);
            
            $result['institutions'][] = $institution;
            $result['degrees'][] = $degree;
            $result['dates'][] = $dates;
            $result['full_entries'][] = [
                'degree' => $degree,
                'institution' => $institution,
                'dates' => $dates
            ];
        }
        // Alternative format fallback
        elseif (preg_match('/^(.*?)\s*(?:,\s*)?(.*?)\s*(?:,\s*)?(.*)$/', $eduText, $altMatch)) {
            $result['degrees'][] = trim($altMatch[1]);
            $result['institutions'][] = trim($altMatch[2]);
            $result['dates'][] = trim($altMatch[3]);
            $result['full_entries'][] = [
                'degree' => trim($altMatch[1]),
                'institution' => trim($altMatch[2]),
                'dates' => trim($altMatch[3])
            ];
        }
        // Fallback: Just store the raw text if parsing fails
        else {
            $result['full_entries'][] = [
                'raw' => $eduText
            ];
        }
    }

    return $result;
}







public function evaluateQuality(string $text): float
{
    $apiScore = $this->callModel(
        $this->models['quality'],
        "Rate resume quality (1-10) considering structure, clarity and relevance: " . substr($text, 0, 1000)
    );
    
    if (isset($apiScore['score'])) {
        return min(10, max(1, $apiScore['score']));
    }
    
    // Enhanced local scoring
    $score = 5;
    $score += min(3, substr_count($text, '•') * 0.2); // Bullet points
    $score += min(2, substr_count($text, 'achieved|developed|improved') * 0.3); // Action verbs
    $score += min(2, preg_match_all('/\d+%|\$\d+/', $text)); // Quantifiable metrics
    return round(min(10, max(1, $score)), 1);
}

 

public function generateRecommendations(string $text): array
{
    $analysis = [
        'has_metrics' => preg_match('/\d+%|\$\d+/', $text),
        'bullet_points' => substr_count($text, '•'),
        'education' => preg_match('/EDUCATION/i', $text),
        'skills_section' => preg_match('/SKILLS/i', $text)
    ];
    
    $recommendations = [];
    
    if (!$analysis['has_metrics']) {
        $recommendations[] = "Add quantifiable achievements (e.g., 'Increased performance by 30%')";
    }
    
    if ($analysis['bullet_points'] < 3) {
        $recommendations[] = "Use more bullet points for better readability";
    }
    
    if (!$analysis['education']) {
        $recommendations[] = "Include an education section with relevant degrees";
    }
    
    if (!$analysis['skills_section']) {
        $recommendations[] = "Add a dedicated skills section";
    }
    
    return array_slice(array_merge($recommendations, [
        "Highlight leadership experiences",
        "Tailor to job description keywords"
    ]), 0, 3);
}
   


    protected function callModel(string $model, string $input): array
{
    try {
        // First check if model exists
        $modelInfo = $this->client->get($this->apiUrl . $model, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiToken]
        ]);
        
        $response = $this->client->post($this->apiUrl . $model, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ],
            'json' => ['inputs' => $input],
            'timeout' => 30
        ]);

        return json_decode($response->getBody()->getContents(), true) ?: [];

    } catch (\GuzzleHttp\Exception\ClientException $e) {
        if ($e->getCode() === 404) {
            return $this->fallbackProcessing($model, $input);
        }
        return ['error' => $e->getMessage()];
    }
}
protected function fallbackProcessing(string $modelType, string $text): array
{
    switch ($modelType) {
        case 'skills':
            return $this->localSkillsExtraction($text);
        case 'quality':
            return ['score' => $this->simpleQualityCheck($text)];
        case 'recommendations':
            return $this->basicRecommendations();
        default:
            return ['error' => 'Model unavailable'];
    }
}

protected function localSkillsExtraction(string $text): array
{
    // Extract skills using regex patterns
    preg_match_all('/\b(?:[A-Z][a-z]+(?: [A-Z][a-z]+)*\b(?=\s+[A-Z][a-z]+|\b)/', $text, $matches);
    return array_unique(array_filter($matches[0] ?? []));
}

protected function simpleQualityCheck(string $text): float
{
    // Basic quality heuristics
    $score = 5.0;
    $score += min(2, substr_count($text, '•') * 0.1); // Bullet points
    $score += min(3, substr_count(strtolower($text), 'achieved') * 0.2); // Action verbs
    return min(10, max(1, $score));
}

protected function basicRecommendations(): array
{
    return [
        'Include more measurable achievements',
        'Add relevant technical keywords',
        'Highlight leadership experiences'
    ];
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