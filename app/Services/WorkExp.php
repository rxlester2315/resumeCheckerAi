<?php

namespace App\Services;

class WorkExp
{
    public function analyze(string $text): array
    {
        $workExperience = $this->getWorkExperience($text);
        return $this->normalizeWorkExperience($workExperience);
    }

    protected function getWorkExperience(string $text): array
    {
        $workExperience = [];
        
        if (preg_match('/(?:WORK\s+)?EXPERIENCE[:]?(.+?)(?=INTERNSHIP|EDUCATION|SKILLS|PROJECTS|$)/is', $text, $match)) {
            $workText = preg_replace('/.*intern.*\n?/i', '', trim($match[1]));
            
            if (!empty($workText)) {
                $workExperience = $this->parseWorkEntries($workText);
            }
        }
        
        return $workExperience;
    }

    protected function parseWorkEntries(string $text): array
    {
        $entries = [];
        
        // Structured format (e.g., "Jan 2020 - Present Company, Position")
        if (preg_match_all('/([A-Z][a-z]+ \d{4} - (?:[A-Z][a-z]+ \d{4}|Present))\s*([^\n]+?)\s*,\s*([^\n]+)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (stripos($match[2], 'intern') === false) {
                    $entries[] = $this->createWorkEntry($match[2], $match[3], $match[1]);
                }
            }
        }
        
        // Bullet point format
        if (preg_match_all('/•\s*([^\n]+?)\s*[,-]\s*([^\n]+?)\s*[,-]\s*([^\n]+?)(?=\n•|\n\n|$)/i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                if (stripos($match[0], 'intern') === false) {
                    $entries[] = $this->createWorkEntry($match[1], $match[2], $match[3]);
                }
            }
        }
        
        return $entries;
    }

    protected function createWorkEntry(string $title, string $company, string $duration): array
    {
        return [
            'title' => trim($title),
            'company' => trim($company),
            'duration' => trim($duration),
            'description' => '',
            'type' => 'work'
        ];
    }

    protected function normalizeWorkExperience(array $entries): array
    {
        return array_map(function ($entry) {
            return [
                'company' => $entry['company'] ?? 'Not specified',
                'description' => $entry['description'] ?? 'No description available',
                'duration' => $entry['duration'] ?? 'Not specified',
                'technologies' => $entry['technologies'] ?? 'Not specified',
                'type' => 'work'
            ];
        }, $entries);
    }
}