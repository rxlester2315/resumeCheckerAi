<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Exception;
use ZipArchive;

class PdfParserService
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function extractTextFromPdf(string $filePath): string
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
            $text = $pdf->getText();
            return $this->cleanText($text);
        } catch (Exception $e) {
            throw new Exception("Failed to read PDF: " . $e->getMessage());
        }
    }

    public function extractTextFromDocx(string $filePath): string
    {
        try {
            $zip = new ZipArchive();
            if ($zip->open($filePath)) {
                if (($index = $zip->locateName('word/document.xml')) !== false) {
                    $content = $zip->getFromIndex($index);
                    $zip->close();
                    return $this->cleanText(strip_tags(str_replace('</w:p>', "\n", $content)));
                }
                $zip->close();
            }
            throw new Exception("Invalid DOCX format - missing document content");
        } catch (Exception $e) {
            throw new Exception("Failed to read DOCX: " . $e->getMessage());
        }
    }

    protected function cleanText(string $text): string
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}