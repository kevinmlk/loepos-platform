<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

// FPDF doesn't use namespaces, so we need to require it
require_once base_path('vendor/setasign/fpdf/fpdf.php');

class PDFSplitService
{
    /**
     * Split a PDF into multiple files based on page ranges
     * 
     * @param string $originalPath The path to the original PDF file
     * @param array $splits Array of split configurations with page ranges
     * @return array Array of split file paths
     */
    public function splitPDF(string $originalPath, array $splits): array
    {
        $splitFiles = [];
        
        // Get the full path to the original PDF
        $fullPath = Storage::disk('public')->path($originalPath);
        
        if (!file_exists($fullPath)) {
            throw new \Exception("Original PDF file not found: {$originalPath}");
        }
        
        foreach ($splits as $index => $split) {
            try {
                $splitFileName = $this->generateSplitFileName($originalPath, $index, $split);
                $splitPath = 'verified_documents/' . $splitFileName;
                $fullSplitPath = Storage::disk('public')->path($splitPath);
                
                // Create a new PDF with only the specified pages
                $pdf = new Fpdi();
                
                // Set the source file
                $pageCount = $pdf->setSourceFile($fullPath);
                
                // Add only the specified pages
                $pagesAdded = 0;
                foreach ($split['pages'] as $pageNumber) {
                    if ($pageNumber > 0 && $pageNumber <= $pageCount) {
                        $templateId = $pdf->importPage($pageNumber);
                        $size = $pdf->getTemplateSize($templateId);
                        
                        // Add a page with the same orientation and size
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                        $pagesAdded++;
                    }
                }
                
                // Log the split operation
                Log::info('Split PDF created', [
                    'original' => $originalPath,
                    'split_path' => $splitPath,
                    'requested_pages' => $split['pages'],
                    'pages_added' => $pagesAdded,
                    'total_pages' => $pageCount
                ]);
                
                // Save the split PDF
                $pdf->Output($fullSplitPath, 'F');
                
                $splitFiles[] = [
                    'path' => $splitPath,
                    'pages' => $split['pages'] ?? [],
                    'name' => $split['name'] ?? $splitFileName
                ];
                
            } catch (\Exception $e) {
                \Log::error('Error splitting PDF with FPDI', [
                    'original' => $originalPath,
                    'split_index' => $index,
                    'error' => $e->getMessage()
                ]);
                
                
                // If splitting fails, copy the original as fallback
                Storage::disk('public')->copy($originalPath, $splitPath);
                
                $splitFiles[] = [
                    'path' => $splitPath,
                    'pages' => $split['pages'] ?? [],
                    'name' => $split['name'] ?? $splitFileName,
                    'error' => 'Failed to split - using original'
                ];
            }
        }
        
        return $splitFiles;
    }
    
    /**
     * Generate a unique filename for the split PDF
     */
    private function generateSplitFileName(string $originalPath, int $index, array $split): string
    {
        $originalName = pathinfo($originalPath, PATHINFO_FILENAME);
        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        $timestamp = now()->format('Ymd_His');
        $uniqueId = Str::random(8);
        
        $pageInfo = '';
        if (!empty($split['pages'])) {
            $pageInfo = '_pages_' . implode('-', $split['pages']);
        }
        
        return "{$originalName}_split_{$index}_{$timestamp}_{$uniqueId}{$pageInfo}.{$extension}";
    }
    
    /**
     * Create the verified documents storage directory if it doesn't exist
     */
    public function ensureStorageDirectoryExists(): void
    {
        if (!Storage::disk('public')->exists('verified_documents')) {
            Storage::disk('public')->makeDirectory('verified_documents');
        }
    }
    
    /**
     * Create PDFs from base64 encoded page images
     * 
     * @param array $pageImagesData Array of base64 encoded images
     * @param string $baseFileName Base name for the output files
     * @param array $splits Array of split configurations with page ranges
     * @return array Array of split file paths
     */
    public function createPDFsFromBase64Images(array $pageImagesData, string $baseFileName, array $splits): array
    {
        $splitFiles = [];
        
        foreach ($splits as $index => $split) {
            try {
                $splitFileName = $this->generateSplitFileNameFromBase($baseFileName, $index, $split);
                $splitPath = 'verified_documents/' . $splitFileName;
                $fullSplitPath = Storage::disk('public')->path($splitPath);
                
                // Create a new PDF with FPDF
                $pdf = new FPDF();
                
                // Add only the specified pages
                foreach ($split['pages'] as $pageNumber) {
                    $pageIndex = $pageNumber - 1; // Convert to 0-based index
                    if (isset($pageImagesData[$pageIndex])) {
                        // Create temporary image file from base64 data
                        $imageData = $pageImagesData[$pageIndex];
                        $tempImagePath = sys_get_temp_dir() . '/' . Str::random(16) . '.png';
                        
                        // Remove data URL prefix if present
                        if (strpos($imageData, 'data:image') === 0) {
                            $imageData = substr($imageData, strpos($imageData, ',') + 1);
                        }
                        
                        file_put_contents($tempImagePath, base64_decode($imageData));
                        
                        // Get image dimensions
                        list($width, $height) = getimagesize($tempImagePath);
                        
                        // Calculate page size to fit image
                        $orientation = $width > $height ? 'L' : 'P';
                        $pdf->AddPage($orientation);
                        
                        // Calculate scaling to fit page
                        $pageWidth = $orientation == 'P' ? 210 : 297; // A4 dimensions in mm
                        $pageHeight = $orientation == 'P' ? 297 : 210;
                        
                        $scale = min($pageWidth / ($width / 3.78), $pageHeight / ($height / 3.78)); // Convert pixels to mm
                        $imgWidth = ($width / 3.78) * $scale;
                        $imgHeight = ($height / 3.78) * $scale;
                        
                        // Center image on page
                        $x = ($pageWidth - $imgWidth) / 2;
                        $y = ($pageHeight - $imgHeight) / 2;
                        
                        // Add image to PDF
                        $pdf->Image($tempImagePath, $x, $y, $imgWidth, $imgHeight);
                        
                        // Clean up temp image
                        unlink($tempImagePath);
                    }
                }
                
                // Save the split PDF
                $pdf->Output('F', $fullSplitPath);
                
                $splitFiles[] = [
                    'path' => $splitPath,
                    'pages' => $split['pages'] ?? [],
                    'name' => $split['name'] ?? $splitFileName
                ];
                
            } catch (\Exception $e) {
                Log::error('Error creating PDF from base64 images', [
                    'base_name' => $baseFileName,
                    'split_index' => $index,
                    'error' => $e->getMessage()
                ]);
                
                throw $e;
            }
        }
        
        return $splitFiles;
    }
    
    /**
     * Generate a unique filename for the split PDF from base name
     */
    private function generateSplitFileNameFromBase(string $baseName, int $index, array $split): string
    {
        $timestamp = now()->format('Ymd_His');
        $uniqueId = Str::random(8);
        
        $pageInfo = '';
        if (!empty($split['pages'])) {
            $pageInfo = '_pages_' . implode('-', $split['pages']);
        }
        
        return "{$baseName}_split_{$index}_{$timestamp}_{$uniqueId}{$pageInfo}.pdf";
    }
    
}