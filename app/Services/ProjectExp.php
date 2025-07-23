<?php

namespace App\Services;

class ProjectExp
{
    public function analyze(string $text): array
    {
        $text = $this->removeIrrelevantSections($text);
        $projectsText = $this->extractProjectsSection($text) ?? $this->fallbackExtractProjects($text);
        
        $projects = [];
        $this->parseProjects($projectsText, $projects);
        
        return $this->normalizeProjects($projects);
    }

    protected function removeIrrelevantSections(string $text): string
    {
        return preg_replace('/^.*?(?=(EDUCATION|EXPERIENCE|PROJECTS|SKILLS))/i', '', $text);
    }

    protected function extractProjectsSection(string $text): ?string
    {
        $patterns = [
            '/PROJECTS[:]?(.+?)(?=EXPERIENCE|EDUCATION|SKILLS|EMPLOYMENT|INTERNSHIP|$)/is',
            '/PROJECT EXPERIENCE[:]?(.+?)(?=WORK|EDUCATION|EMPLOYMENT|INTERNSHIP|$)/is'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $match) && 
                !preg_match('/(internship|employment|work experience)/i', $match[1])) {
                return trim($match[1]);
            }
        }
        return null;
    }

    protected function fallbackExtractProjects(string $text): string
    {
        $lines = explode("\n", $text);
        $start = (int) floor(count($lines) * 0.3); // Skip first 30% (contact info)
        return implode("\n", array_slice($lines, $start));
    }

    protected function parseProjects(string $text, array &$projects): void
    {
        $this->parseBulletPointProjects($text, $projects);
        $this->parseHeaderBasedProjects($text, $projects);
        $this->parseImplicitProjects($text, $projects);
    }

    protected function parseBulletPointProjects(string $text, array &$projects): void
    {
        $text = preg_replace('/^\s*(name|contact|phone|email|github|portfolio)\s*[:.]?\s*.+$/im', '', $text);
        $entries = preg_split('/(?:^|\n)(?:•|\d+\.|\-)\s*/', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        foreach ($entries as $entry) {
            $entry = trim($entry);
            if (empty($entry) )
            continue;
            
            if (preg_match('/^https?:\/\/[^\s]+$/i', $entry)) continue;
            
            if (preg_match('/^(.+?)\s+(https?:\/\/(?:www\.)?github\.com\/\S+)$/i', $entry, $match)) {
                $projects[] = $this->createProject($match[1], '', $match[2]);
                continue;
            }
            
            if (preg_match('/^([^\n:-]+?)\s*[:-]\s*(.+)/s', $entry, $match)) {
                $projects[] = $this->createProject($match[1], $match[2]);
            } else {
                $projects[] = $this->createProject($entry);
            }
        }
    }

    protected function createProject(string $name, string $description = '', ?string $github = null): array
    {
        return [
            'name' => trim($name),
            'description' => trim($description),
            ...($github ? ['github' => trim($github)] : []),
            'type' => 'project'
        ];
    }

    protected function normalizeProjects(array $projects): array
    {
        $cleaned = $this->cleanProjectsList($projects);
        $filtered = array_values(array_filter($cleaned, [$this, 'isValidProject']));
        
        return array_map(function ($project) {
            return [
                'company' => $this->determineProjectType($project),
                'description' => $project['description'] ?? '',
                'technologies' => $this->extractTechnologies($project['description'] ?? ''),
                'type' => 'project'
            ];
        }, $filtered);
    }

    protected function cleanProjectsList(array $projects): array
    {
        $unique = [];
        $seen = [];
        
        foreach ($projects as $project) {
            $name = trim($project['name'] ?? '');
            $description = $this->cleanDescription($project['description'] ?? '');
            
            if (empty($name) && empty($description)) continue;
            
            $key = md5(strtolower($name . substr($description, 0, 20)));
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $unique[] = ['name' => $name, 'description' => $description];
            }
        }
        
        return $unique;
    }

    protected function cleanDescription(string $description): string
    {
        return trim(preg_replace(
            '/\bhttps?:\/\/(www\.)?github\.com\/[a-z0-9_-]+\/?\b/i', 
            '', 
            $description
        ));
    }

    protected function isValidProject(array $project): bool
    {
        $isGitHubLink = fn($val) => preg_match('/^(https?:\/\/)?(www\.)?github\.com\/[a-z0-9_-]+\/?$/i', trim($val));
        
        return !$isGitHubLink($project['name'] ?? '') &&
               !$isGitHubLink($project['description'] ?? '');
    }

    protected function determineProjectType(array $project): string
    {
        $name = strtolower($project['name'] ?? '');
        $desc = strtolower($project['description'] ?? '');

        if (str_contains($name, 'capstone') || str_contains($desc, 'capstone')) {
            return 'Capstone Project';
        }
        if (str_contains($name, 'freelance') || str_contains($desc, 'freelance')) {
            return 'Freelance Project';
        }
        if (str_contains($name, 'academic') || str_contains($desc, 'university')) {
            return 'Academic Project';
        }
        if (str_contains($name, 'hackathon') || str_contains($desc, 'hackathon')) {
            return 'Hackathon Project';
        }
        if (preg_match('/(?:for|at|with)\s+([^\s,]+)/i', $name, $match)) {
            return ucfirst($match[1]);
        }
        
        return 'Personal Project';
    }

    protected function extractTechnologies(string $description): string
    {
        preg_match_all('/\b(?:PHP|Laravel|Vue(?:\.js)?|React(?:\.js)?|JavaScript|Python|MySQL|Git)\b/i', $description, $matches);
        $tech = array_unique(array_map('strtolower', $matches[0] ?? []));
        return $tech ? implode(', ', $tech) : 'Various technologies';
    }
}