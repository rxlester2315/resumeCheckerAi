<?php

namespace App\Services;

class ProjectExperience
{
    public static function parse(string $projectsText): array
    {
        // Split projects by double newlines or bullet points
        $projects = preg_split('/\n\s*\n|(?=•)/', $projectsText);
        
        $parsedProjects = [];
        
        foreach ($projects as $project) {
            if (trim($project)) {
                $parsedProjects[] = [
                    'name' => self::extractProjectName($project),
                    'technologies' => self::extractTechnologies($project),
                    'description' => self::cleanDescription($project),
                    'duration' => self::extractDuration($project),
                    'role' => self::extractRole($project)
                ];
            }
        }
        
        return $parsedProjects;
    }
    
    protected static function extractProjectName(string $text): string
    {
        // First line is often the project name
        $lines = explode("\n", $text);
        return trim($lines[0] ?? '');
    }
    
    protected static function extractTechnologies(string $text): array
    {
        $technologies = [];
        // Match common tech terms
        preg_match_all('/\b(?:PHP|Laravel|Vue|React|JavaScript|Python|MySQL|Git|Node\.?js|Docker|AWS)\b/i', $text, $matches);
        $technologies = $matches[0] ?? [];
        
        // Match capitalized tech terms
        preg_match_all('/\b[A-Z][a-z]+(?: [A-Z][a-z]+)*\b/', $text, $capMatches);
        
        return array_unique(array_merge(
            array_map('strtolower', $technologies),
            $capMatches[0] ?? []
        ));
    }
    
    protected static function cleanDescription(string $text): string
    {
        // Remove project name from description
        $lines = explode("\n", $text);
        if (count($lines) > 1) {
            array_shift($lines); // Remove first line (project name)
        }
        return trim(implode("\n", $lines));
    }
    
    protected static function extractDuration(string $text): string
    {
        if (preg_match('/(\d{4}\s*[-–]\s*(?:present|\d{4}))/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
    
    protected static function extractRole(string $text): string
    {
        if (preg_match('/\b(?:as|role)\s*:\s*(.+?)(?:\n|$)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
}