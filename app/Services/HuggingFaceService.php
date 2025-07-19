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
        // Extract each type of experience independently
        $workExperience = $this->getWorkExperience($text);
        $internshipExperience = $this->getInternshipExperience($text);
        $projectExperience = $this->getProjectExperience($text);
        
        // Determine what to show based on what's available
        $result = $this->determineExperienceDisplay($workExperience, $internshipExperience, $projectExperience);
        
        return $result;
    }

    protected function getWorkExperience(string $text): array
    {
        $workExperience = [];
        
        // Look for work experience section
        if (preg_match('/(?:WORK\s+)?EXPERIENCE[:]?(.+?)(?=INTERNSHIP|EDUCATION|SKILLS|PROJECTS|$)/is', $text, $match)) {
            $workText = trim($match[1]);
            
            // Filter out internship entries
            $workText = preg_replace('/.*intern.*\n?/i', '', $workText);
            
            if (!empty(trim($workText))) {
                $workExperience = $this->parseWorkEntries($workText);
            }
        }
        
        return $this->normalizeExperience($workExperience, 'work');
    }

    /**
     * NEW: Determines what experience sections to display
     */
    protected function determineExperienceDisplay(array $workExp, array $internshipExp, array $projectExp): array
    {
        $result = [
            'has_work_experience' => !empty($workExp),
            'has_internship_experience' => !empty($internshipExp),
            'has_project_experience' => !empty($projectExp),
            'display_sections' => []
        ];

        // If there's formal work experience, show all sections
        if (!empty($workExp)) {
            $result['display_sections'] = [
                'work_experience' => $workExp,
                'internship_experience' => $internshipExp,
                'project_experience' => $projectExp
            ];
        } 
        // If no formal work experience, only show internships and projects
        else {
            $result['display_sections'] = [
                'internship_experience' => $internshipExp,
                'project_experience' => $projectExp
            ];
        }

        return $result;
    }

    /**
     * NEW: Extract only formal work experience (excluding internships)
     */
    

    /**
     * NEW: Extract only internship experiences
     */
    protected function getInternshipExperience(string $text): array
    {
        $internships = [];
        
        // First try to find a dedicated internship section
        $internshipText = $this->extractInternshipSection($text);
        
        // If no dedicated section, look for internships in experience section
        if (empty($internshipText)) {
            if (preg_match('/EXPERIENCE[:]?(.+?)(?=EDUCATION|SKILLS|PROJECTS|$)/is', $text, $match)) {
                $experienceText = $match[1];
                // Extract only lines mentioning internships
                preg_match_all('/.*intern.*\n?/i', $experienceText, $internshipMatches);
                $internshipText = implode('\n', $internshipMatches[0] ?? []);
            }
        }

        if (!empty($internshipText)) {
            $this->parseStructuredInternships($internshipText, $internships);
            $this->parseBulletPointInternships($internshipText, $internships);
        }
        
        return $this->normalizeExperience($this->cleanInternshipList($internships), 'internship');
    }


protected function getProjectExperience(string $text): array
{
        $text = preg_replace('/^.*?(?=(EDUCATION|EXPERIENCE|PROJECTS|SKILLS))/i', '', $text);

    // First try to find a dedicated projects section
    $projectsText = $this->extractProjectsSection($text);

    // If no dedicated section, scan the entire text for project mentions
    if (empty($projectsText)) {
        // Skip the top 30% of the resume (likely to contain name, email, GitHub, etc.)
        $lines = explode("\n", $text);
        $start = (int) floor(count($lines) * 0.3);
        $projectsText = implode("\n", array_slice($lines, $start));
    }

    // ✅ Initialize projects list
    $projects = [];
    $this->parseBulletPointProjects($projectsText, $projects);
    $this->parseHeaderBasedProjects($projectsText, $projects);
    $this->parseImplicitProjects($projectsText, $projects);

    // ✅ Clean and separate each project
    $cleanProjects = $this->cleanProjectsList($projects);

    // ✅ Final filter to remove GitHub-only or malformed entries
    $filtered = array_values(array_filter($cleanProjects, function ($project) {
        $isGitHubLink = fn($val) => is_string($val) &&
            preg_match('/^(https?:\/\/)?(www\.)?github\.com\/[a-zA-Z0-9_-]+$/i', trim($val));

        // Remove if company or description is just a GitHub profile link
        if (
            (isset($project['company']) && $isGitHubLink($project['company'])) ||
            (isset($project['description']) && $isGitHubLink($project['description']))
        ) {
            return false;
        }

        // Remove if all fields are GitHub links or empty
        $nonEmpty = array_filter($project, fn($v) => !empty(trim((string) $v)));
        if (count($nonEmpty) > 0 && array_reduce($nonEmpty, fn($carry, $val) => $carry && $isGitHubLink($val), true)) {
            return false;
        }

        return true;
    }));

    // Normalize and return
    return $this->normalizeExperience($filtered, 'project');
}



    
    
    protected function parseWorkEntries(string $text): array
    {
        $workEntries = [];
        
        // Try structured format first
        if (preg_match_all('/([A-Z][a-z]+ \d{4} - (?:[A-Z][a-z]+ \d{4}|Present))\s*([^\n]+?)\s*,\s*([^\n]+)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Skip if it mentions intern
                if (stripos($match[2], 'intern') !== false) continue;
                
                $workEntries[] = [
                    'title' => trim($match[2]),
                    'company' => trim($match[3]),
                    'duration' => trim($match[1]),
                    'description' => '',
                    'type' => 'work'
                ];
            }
        }
        
        // Try bullet point format
        if (preg_match_all('/•\s*([^\n]+?)\s*[,-]\s*([^\n]+?)\s*[,-]\s*([^\n]+?)(?=\n•|\n\n|$)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Skip if it mentions intern
                if (stripos($match[0], 'intern') !== false) continue;
                
                $workEntries[] = [
                    'title' => trim($match[1]),
                    'company' => trim($match[2]),
                    'duration' => trim($match[3]),
                    'description' => '',
                    'type' => 'work'
                ];
            }
        }
        
        return $workEntries;
    }

    /**
     * Normalizes experience entries to consistent structure
     */

protected function normalizeExperience(array $entries, string $type): array
{
    $normalized = [];
    
    foreach ($entries as $item) {
        // Handle project format
 $name = $item['name'] ?? '';
    $description = $item['description'] ?? '';

         // 🚫 Skip if description is a GitHub link or very short
    if (preg_match('/^https?:\/\/(www\.)?github\.com\/[^\s]*$/i', trim($description))) {
        continue;
    }

    // 🚫 Skip if name is a URL
    if (preg_match('/^https?:\/\/(www\.)?github\.com\/[^\s]*$/i', trim($name))) {
        continue;
    }

    // 🚫 Skip if both name and description are very short or contain no words
    if (strlen(strip_tags($name)) < 5 && strlen(strip_tags($description)) < 10) {
        continue;
    }

    
        if (isset($item['name'])) {
            $company = $this->determineProjectCompany($item);
            
            // Check if description contains multiple projects
            $projectDescriptions = $this->splitCombinedProjects($item['description'] ?? '');
            
            if (count($projectDescriptions) > 1) {
                foreach ($projectDescriptions as $desc) {
                    $normalized[] = [
                        'company' => $this->determineProjectCompany(['name' => $item['name'], 'description' => $desc]),
                        'description' => $desc,
                        'duration' => $item['duration'] ?? 'Not specified',
                        'technologies' => $this->extractTechnologiesFromDescription($desc),
                        'type' => $type
                    ];
                }
            } else {
                $normalized[] = [
                    'company' => $company,
                    'description' => $item['description'] ?? 'No description available',
                    'duration' => $item['duration'] ?? 'Not specified',
                    'technologies' => $item['technologies'] ?? $this->extractTechnologiesFromDescription($item['description'] ?? ''),
                    'type' => $type
                ];
            }
        }
        // Handle other formats (work, internship)
        else {
            $normalized[] = [
                'company' => $item['company'] ?? 'Not specified',
                'description' => $item['description'] ?? 'No description available',
                'duration' => $item['duration'] ?? 'Not specified',
                'technologies' => $item['technologies'] ?? 'No technologies specified',
                'type' => $item['type'] ?? $type
            ];
        }
    }
    
    return $normalized;
}

protected function determineProjectCompany(array $project): string
{
    $name = strtolower($project['name'] ?? '');
    $description = strtolower($project['description'] ?? '');

    if (str_contains($name, 'personal project') || str_contains($description, 'personal project')) {
    return 'Personal Project';
}


    // 2. Check for freelance indicators
    if (str_contains($name, 'freelance') || 
        str_contains($description, 'freelance') ||
        str_contains($description, 'client') ||
        str_contains($description, 'contract') ||
        str_contains($description, 'consulting')) {
        return 'Freelance Project';
    }

    // 3. Check for academic/school projects
    if (str_contains($name, 'academic') || 
        str_contains($description, 'university') ||
        str_contains($description, 'school') ||
        str_contains($description, 'course') ||
        str_contains($description, 'college') ||
        str_contains($description, 'curriculum')) {
        return 'Academic Project';
    }
    // 1. First check for Capstone projects (highest priority)
    if (str_contains($name, 'capstone') || 
        str_contains($description, 'capstone') ||
        str_contains($description, 'final year project') ||
        str_contains($description, 'senior project')) {
        return 'Capstone Project';
    }

    // 4. Check for hackathon projects
    if (str_contains($name, 'hackathon') || 
        str_contains($description, 'hackathon')) {
        return 'Hackathon Project';
    }

    // 5. Check if company name is mentioned in description
    if (preg_match('/(?:for|at|with)\s+(.+?)(?:\s+(?:company|corp|inc|llc)|[,\.]|$)/i', $name, $matches)) {
        $company = trim($matches[1]);
        // Filter out false positives
        if (!in_array(strtolower($company), ['client', 'university', 'school'])) {
            return $company;
        }
    }

    // 6. Default to Personal Project if no indicators found
    return 'Personal Project';
}


protected function splitCombinedProjects(string $description): array
{
    // Just split the description directly
    $projects = preg_split('/\s{2,}|\n\s*\n|(?<=\.)\s+(?=\w)/', $description);

    // Trim and remove empty entries
    return array_filter(array_map('trim', $projects));
}



    /**
     * NEW: Extract technologies from project description
     */
    protected function extractTechnologiesFromDescription(string $description): string
    {
        // Common tech patterns
        preg_match_all('/\b(?:PHP|Laravel|Vue(?:\.js)?|React(?:\.js)?|JavaScript|Python|MySQL|Git|Node(?:\.js)?|Docker|AWS|HTML|CSS|Bootstrap|jQuery)\b/i', $description, $matches);
        
        $technologies = array_unique(array_map('strtolower', $matches[0] ?? []));
        
        return !empty($technologies) ? implode(', ', $technologies) : 'Various technologies';
    }

    // Keep all existing internship extraction methods
    protected function extractInternshipSection(string $text): string
    {
        $patterns = [
            '/INTERNSHIPS?:?(.+?)(?=EXPERIENCE|EDUCATION|SKILLS|EMPLOYMENT|PROJECTS|$)/is',
            '/INTERNSHIP EXPERIENCE:?(.+?)(?=WORK|EDUCATION|EMPLOYMENT|PROJECTS|$)/is'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match)) {
                return trim($match[1]);
            }
        }

        return '';
    }

    protected function parseStructuredInternships(string $text, array &$internships): void
    {
        // Look for structured internship entries
        if (preg_match_all('/([A-Z][a-z]+ \d{4} - (?:[A-Z][a-z]+ \d{4}|Present))\s*([^\n]+)\s*Intern\s*,\s*([^\n]+)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $internships[] = [
                    'title' => 'Intern',
                    'company' => trim($match[3]),
                    'duration' => trim($match[1]),
                    'description' => trim($match[2]),
                    'type' => 'internship'
                ];
            }
        }
    }

    protected function parseBulletPointInternships(string $text, array &$internships): void
    {
        // Look for bullet point internship entries
        if (preg_match_all('/•\s*(.*?Intern.*?)\s*[,-]\s*(.*?)\s*[,-]\s*(.*?)(?=\n•|\n\n|$)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $internships[] = [
                    'title' => trim($match[1]),
                    'company' => trim($match[2]),
                    'duration' => trim($match[3]),
                    'description' => '',
                    'type' => 'internship'
                ];
            }
        }
        
        // Alternative bullet point format
        if (preg_match_all('/•\s*(.*?)\s*Intern\s*,\s*(.*?)\s*\((.*?)\)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $internships[] = [
                    'title' => trim($match[1]) . ' Intern',
                    'company' => trim($match[2]),
                    'duration' => trim($match[3]),
                    'description' => '',
                    'type' => 'internship'
                ];
            }
        }
    }

    protected function cleanInternshipList(array $internships): array
    {
        $uniqueInternships = [];
        $seen = [];
        
        foreach ($internships as $internship) {
            // Basic validation
            if (empty($internship['company']) || empty($internship['title'])) {
                continue;
            }
            
            // Normalize for comparison
            $key = strtolower($internship['company'] . '|' . $internship['title']);
            
            // Skip duplicates
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueInternships[] = $internship;
            }
        }
        
        return $uniqueInternships;
    }

    // Keep all existing project extraction methods
   protected function extractProjectsSection(string $text): string
{
    // First remove profile/contact section
    $text = preg_replace('/^(.*?)(?=EDUCATION|EXPERIENCE|PROJECTS|SKILLS)/is', '', $text);
    
    $patterns = [
        '/PROJECTS[:]?(.+?)(?=EXPERIENCE|EDUCATION|SKILLS|EMPLOYMENT|INTERNSHIP|$)/is',
        '/PROJECT EXPERIENCE[:]?(.+?)(?=WORK|EDUCATION|EMPLOYMENT|INTERNSHIP|$)/is',
        '/SELECTED PROJECTS[:]?(.+?)(?=EMPLOYMENT|EDUCATION|INTERNSHIP|$)/is',
        '/ACADEMIC PROJECTS[:]?(.+?)(?=INTERNSHIP|EDUCATION|$)/is'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $text, $match)) {
            $content = trim($match[1]);
            if (!preg_match('/(internship|employment|work experience)/i', $content)) {
                return $content;
            }
        }
    }

    return '';
}

protected function parseBulletPointProjects(string $text, array &$projects): void
{
    // Split by project indicators
       $text = preg_replace('/^\s*(name|contact|phone|email|github|portfolio)\s*[:.]?\s*.+$/im', '', $text);
    $projectEntries = preg_split('/(?:^|\n)(?:•|\d+\.|\-)\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
    
    foreach ($projectEntries as $entry) {
        $entry = trim($entry);
        if (empty($entry)) continue;
        
        // Skip if it's just a URL
        if (preg_match('/^https?:\/\/[^\s]+$/i', $entry)) {
            continue;
        }
        
        // Handle entries that might contain GitHub URLs at the end
        if (preg_match('/^(.+?)\s+(https?:\/\/(?:www\.)?github\.com\/\S+)$/i', $entry, $match)) {
            $projects[] = [
                'name' => trim($match[1]),
                'description' => '', // Or you could parse the description part
                'github' => trim($match[2]), // Store GitHub URL separately
                'type' => 'project',
                'source' => 'bullet_points'
            ];
            continue;
        }
        
        // Standard project format
        if (preg_match('/^([^\n:-]+?)\s*[:-]\s*(.+)/s', $entry, $match)) {
            $description = trim($match[2]);
            
            // Check if description contains a GitHub URL
            if (preg_match('/^(.*?)\s*(https?:\/\/(?:www\.)?github\.com\/\S+)$/i', $description, $descMatch)) {
                $projects[] = [
                    'name' => trim($match[1]),
                    'description' => trim($descMatch[1]),
                    'github' => trim($descMatch[2]),
                    'type' => 'project',
                    'source' => 'bullet_points'
                ];
            } else {
                $projects[] = [
                    'name' => trim($match[1]),
                    'description' => $description,
                    'type' => 'project',
                    'source' => 'bullet_points'
                ];
            }
        } else {
            $projects[] = [
                'name' => $entry,
                'description' => '',
                'type' => 'project',
                'source' => 'bullet_points'
            ];
        }
    }
}

    protected function parseHeaderBasedProjects(string $text, array &$projects): void
    {
        // Project title followed by description
        if (preg_match_all('/(?:^|\n)([A-Z][^\n:]+?)\s*\n([^\n]+)(?=\n[A-Z]|\n\s*\n|$)/', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $projects[] = [
                    'name' => trim($match[1]),
                    'description' => trim($match[2]),
                    'type' => 'project',
                    'source' => 'header_based'
                ];
            }
        }
    }

    protected function parseGitHubStyleProjects(string $text, array &$projects): void
    {
        // GitHub-style project listings
        if (preg_match_all('/\*\s*\[([^\]]+)\]\([^\)]+\)\s*-\s*([^\n]+)/', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $projects[] = [
                    'name' => trim($match[1]),
                    'description' => trim($match[2]),
                    'type' => 'project',
                    'source' => 'github_style'
                ];
            }
        }
    }

    protected function parseImplicitProjects(string $text, array &$projects): void
    {
        // Look for action verbs that might indicate projects
        $verbs = ['built', 'developed', 'created', 'designed', 'implemented'];
        $pattern = '/(' . implode('|', $verbs) . ')\s+(?:an?\s)?([^\n.,;]+)/i';
        
        if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $projects[] = [
                    'name' => ucfirst(trim($match[1] . ' ' . $match[2])),
                    'description' => 'Mentioned in experience section',
                    'type' => 'implicit',
                    'source' => 'action_verbs'
                ];
            }
        }
    }

protected function cleanProjectsList(array $projects): array 
{
    $uniqueProjects = [];
    $seen = [];
    
    foreach ($projects as $project) {
        $name = trim($project['name'] ?? '');
        $description = trim($project['description'] ?? '');
        
        // Skip if the NAME is just a GitHub profile URL
        if (preg_match('/^https?:\/\/(www\.)?github\.com\/[a-z0-9_-]+\/?$/i', $name)) {
            continue;
        }
        
        // Clean GitHub profile URLs from DESCRIPTION (keep other URLs)
        $description = preg_replace(
            '/\bhttps?:\/\/(www\.)?github\.com\/[a-z0-9_-]+\/?\b/i', 
            '', 
            $description
        );
        $description = trim($description);
        
        // Skip if both name and description are empty after cleaning
        if (empty($name) && empty($description)) {
            continue;
        }
        
        // Minimum content requirements
        $hasValidName = strlen($name) >= 3 && preg_match('/[a-z]{3}/i', $name);
        $hasValidDesc = strlen($description) >= 5;
        
        if (!$hasValidName && !$hasValidDesc) {
            continue;
        }
        
        // Update the cleaned description
        $project['description'] = $description;
        
        // Duplicate check (name + first 20 chars of description)
        $key = md5(strtolower($name . substr($description, 0, 20)));
        if (!isset($seen[$key])) {
            $seen[$key] = true;
            $uniqueProjects[] = $project;
        }
        // After cleaning description...
if (empty($name) || preg_match('/^https?:\/\/(www\.)?github\.com\/[a-z0-9_-]+\/?$/i', $description)) {
    continue;
}
    }
    
    return $uniqueProjects;
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