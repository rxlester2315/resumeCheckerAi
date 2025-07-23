<?php

namespace App\Services;


class ExperienceParser
{
    public static function parse(string $experienceText): array
    {
        // Split into individual job entries
        $entries = preg_split('/(?=\d{4}\s*[-–]\s*(?:present|\d{4}))/i', $experienceText);
        
        $parsedExperiences = [];
        
        foreach ($entries as $entry) {
            if (trim($entry)) {
                $parsedExperiences[] = [
                    'title' => self::extractJobTitle($entry),
                    'company' => self::extractCompany($entry),
                    'duration' => self::extractDuration($entry),
                    'description' => self::cleanDescription($entry),
                    'achievements' => self::extractAchievements($entry)
                ];
            }
        }
        
        return $parsedExperiences;
    }
    
    protected static function extractJobTitle(string $text): string
    {
        if (preg_match('/^(.*?)\s*(?:at|@|\||\b)\s*(.*?)$/im', $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
    
    protected static function extractCompany(string $text): string
    {
        if (preg_match('/\bat\s+(.*?)(?:\s*[-–]\s*\d{4}|\s*\(|$)/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
    
    protected static function extractDuration(string $text): string
    {
        if (preg_match('/(\d{4}\s*[-–]\s*(?:present|\d{4}))/i', $text, $matches)) {
            return trim($matches[1]);
        }
        return '';
    }
    
    protected static function cleanDescription(string $text): string
    {
        // Remove job title and company info
        $cleaned = preg_replace('/^(.*?)\s*(?:at|@|\||\b)\s*(.*?)$/im', '', $text);
        // Remove duration
        $cleaned = preg_replace('/(\d{4}\s*[-–]\s*(?:present|\d{4}))/i', '', $cleaned);
        // Clean up remaining text
        return trim(preg_replace('/\s+/', ' ', $cleaned));
    }
    
    protected static function extractAchievements(string $text): array
    {
        $achievements = [];
        // Match bullet points or lines starting with special characters
        if (preg_match_all('/(?:•|\*|\-)\s*(.+?)(?=\n|$)/', $text, $matches)) {
            $achievements = $matches[1];
        }
        return array_map('trim', $achievements);
    }
}