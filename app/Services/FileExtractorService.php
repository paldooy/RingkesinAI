<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser as PdfParser;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Exception;

class FileExtractorService
{
    /**
     * Extract text content from uploaded file.
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    public function extractText(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        return match($extension) {
            'pdf' => $this->extractFromPdf($file),
            'doc', 'docx' => $this->extractFromWord($file),
            'txt' => $this->extractFromTxt($file),
            'html', 'htm' => $this->extractFromHtml($file),
            default => throw new Exception("Tipe file tidak didukung: {$extension}")
        };
    }

    /**
     * Extract text from PDF file.
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    private function extractFromPdf(UploadedFile $file): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());
            $text = $pdf->getText();
            
            if (empty(trim($text))) {
                throw new Exception("File PDF kosong atau tidak bisa dibaca");
            }
            
            return $this->cleanText($text);
        } catch (Exception $e) {
            throw new Exception("Gagal membaca file PDF: " . $e->getMessage());
        }
    }

    /**
     * Extract text from Word document.
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    private function extractFromWord(UploadedFile $file): string
    {
        try {
            // Suppress XML warnings for complex documents
            $prevErrorLevel = error_reporting();
            error_reporting($prevErrorLevel & ~E_WARNING);
            libxml_use_internal_errors(true);
            
            try {
                $phpWord = WordIOFactory::load($file->getRealPath());
            } catch (\Exception $e) {
                // If PhpWord fails, try alternative: extract from XML directly
                return $this->extractFromWordZip($file);
            } finally {
                error_reporting($prevErrorLevel);
                libxml_clear_errors();
            }
            
            $text = '';
            
            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $elementClass = get_class($element);
                    
                    // Handle TextRun
                    if ($elementClass === 'PhpOffice\PhpWord\Element\TextRun') {
                        foreach ($element->getElements() as $textElement) {
                            if (method_exists($textElement, 'getText')) {
                                $text .= $textElement->getText() . " ";
                            }
                        }
                        $text .= "\n";
                    }
                    // Handle Text
                    elseif ($elementClass === 'PhpOffice\PhpWord\Element\Text') {
                        $text .= $element->getText() . "\n";
                    }
                    // Handle Table
                    elseif ($elementClass === 'PhpOffice\PhpWord\Element\Table') {
                        $text .= $this->extractTableText($element) . "\n";
                    }
                    // Handle other elements with getText
                    elseif (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    }
                }
            }
            
            if (empty(trim($text))) {
                throw new Exception("File Word kosong atau tidak bisa dibaca");
            }
            
            return $this->cleanText($text);
        } catch (Exception $e) {
            throw new Exception("Gagal membaca file Word: " . $e->getMessage());
        }
    }

    /**
     * Extract text from Word document using ZIP method (fallback).
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    private function extractFromWordZip(UploadedFile $file): string
    {
        $zip = new \ZipArchive();
        
        if ($zip->open($file->getRealPath()) !== true) {
            throw new Exception("Tidak dapat membuka file Word sebagai ZIP");
        }
        
        // Read document.xml
        $content = $zip->getFromName('word/document.xml');
        $zip->close();
        
        if ($content === false) {
            throw new Exception("Tidak dapat membaca konten dokumen");
        }
        
        // Remove XML tags and extract text
        $content = strip_tags($content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_XML1, 'UTF-8');
        
        return $this->cleanText($content);
    }

    /**
     * Extract text from table element.
     *
     * @param mixed $table
     * @return string
     */
    private function extractTableText($table): string
    {
        $text = '';
        
        try {
            foreach ($table->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . " | ";
                        } elseif (method_exists($element, 'getElements')) {
                            foreach ($element->getElements() as $childElement) {
                                if (method_exists($childElement, 'getText')) {
                                    $text .= $childElement->getText() . " ";
                                }
                            }
                        }
                    }
                }
                $text .= "\n";
            }
        } catch (\Exception $e) {
            // Ignore table extraction errors
        }
        
        return $text;
    }

    /**
     * Extract text from plain text file.
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    private function extractFromTxt(UploadedFile $file): string
    {
        try {
            $text = file_get_contents($file->getRealPath());
            
            if ($text === false || empty(trim($text))) {
                throw new Exception("File TXT kosong atau tidak bisa dibaca");
            }
            
            return $this->cleanText($text);
        } catch (Exception $e) {
            throw new Exception("Gagal membaca file TXT: " . $e->getMessage());
        }
    }

    /**
     * Extract text from HTML file.
     *
     * @param UploadedFile $file
     * @return string
     * @throws Exception
     */
    private function extractFromHtml(UploadedFile $file): string
    {
        try {
            $html = file_get_contents($file->getRealPath());
            
            if ($html === false || empty(trim($html))) {
                throw new Exception("File HTML kosong atau tidak bisa dibaca");
            }
            
            // Strip HTML tags to get plain text
            $text = strip_tags($html);
            
            // Decode HTML entities
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            return $this->cleanText($text);
        } catch (Exception $e) {
            throw new Exception("Gagal membaca file HTML: " . $e->getMessage());
        }
    }

    /**
     * Clean and normalize extracted text.
     *
     * @param string $text
     * @return string
     */
    private function cleanText(string $text): string
    {
        // Remove excessive whitespace
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Remove null bytes
        $text = str_replace("\0", '', $text);
        
        // Trim
        $text = trim($text);
        
        return $text;
    }

    /**
     * Get file size in human readable format.
     *
     * @param UploadedFile $file
     * @return string
     */
    public function getReadableFileSize(UploadedFile $file): string
    {
        $bytes = $file->getSize();
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
