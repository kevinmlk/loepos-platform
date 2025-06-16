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
        
        // Handle both absolute and relative paths
        $relativePath = $originalPath;
        
        if (str_starts_with($originalPath, '/')) {
            // It's an absolute path
            $fullPath = $originalPath;
            
            // Try to extract relative path for storage operations
            $storagePath = storage_path('app/public/');
            if (str_starts_with($originalPath, $storagePath)) {
                $relativePath = str_replace($storagePath, '', $originalPath);
            } else {
                // Use basename as fallback
                $relativePath = 'documents/' . basename($originalPath);
            }
        } else {
            // It's a relative path
            $relativePath = $originalPath;
            $fullPath = Storage::disk('public')->path($originalPath);
        }
        
        if (!file_exists($fullPath)) {
            throw new \Exception("Original PDF file not found: {$fullPath}");
        }
        
        foreach ($splits as $index => $split) {
            try {
                $splitFileName = $this->generateSplitFileName($relativePath, $index, $split);
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
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e)
                ]);
                
                // Try GhostScript as fallback
                $gsResult = $this->splitWithGhostScript($fullPath, $split['pages'], $fullSplitPath);
                
                if ($gsResult) {
                    Log::info('Successfully split PDF using GhostScript', [
                        'split_path' => $splitPath,
                        'pages' => $split['pages']
                    ]);
                    
                    $splitFiles[] = [
                        'path' => $splitPath,
                        'pages' => $split['pages'] ?? [],
                        'name' => $split['name'] ?? $splitFileName,
                        'method' => 'ghostscript'
                    ];
                } else {
                    // If all methods fail, mark as failed - DO NOT use original
                    Log::error('All PDF split methods failed', [
                        'original' => $originalPath,
                        'split_path' => $splitPath
                    ]);
                    
                    $splitFiles[] = [
                        'path' => null,
                        'pages' => $split['pages'] ?? [],
                        'name' => $split['name'] ?? $splitFileName,
                        'error' => 'Failed to split PDF'
                    ];
                }
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
     * Split PDF using GhostScript
     * 
     * @param string $inputPath The input PDF file path
     * @param array $pages Array of page numbers to extract
     * @param string $outputPath The output file path
     * @return bool Success status
     */
    private function splitWithGhostScript(string $inputPath, array $pages, string $outputPath): bool
    {
        // Check if GhostScript is available - try multiple locations
        $gsPath = null;
        $possiblePaths = [
            '/opt/homebrew/bin/gs',
            '/usr/local/bin/gs',
            '/usr/bin/gs',
            'gs'
        ];
        
        foreach ($possiblePaths as $path) {
            exec("$path --version 2>&1", $output, $returnCode);
            if ($returnCode === 0) {
                $gsPath = $path;
                break;
            }
        }
        
        if (!$gsPath) {
            Log::warning('GhostScript not available in any known location');
            return false;
        }
        
        try {
            // For GhostScript, we need to extract specific pages
            // If pages are not consecutive, we'll create a temporary file
            sort($pages);
            
            // Build the page list for GhostScript
            $pageList = [];
            foreach ($pages as $page) {
                $pageList[] = $page;
            }
            $pageString = implode(' ', $pageList);
            
            // Extract each page individually then merge them
            $tempFiles = [];
            $tempDir = sys_get_temp_dir();
            
            // Step 1: Extract each page to a temporary file
            foreach ($pages as $page) {
                $tempFile = $tempDir . '/pdf_page_' . $page . '_' . uniqid() . '.pdf';
                $extractCommand = sprintf(
                    '%s -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER -dFirstPage=%d -dLastPage=%d -sOutputFile=%s %s 2>&1',
                    $gsPath,
                    $page,
                    $page,
                    escapeshellarg($tempFile),
                    escapeshellarg($inputPath)
                );
                
                Log::info('Extracting page', [
                    'page' => $page,
                    'command' => $extractCommand
                ]);
                
                exec($extractCommand, $pageOutput, $pageReturn);
                
                if ($pageReturn === 0 && file_exists($tempFile) && filesize($tempFile) > 0) {
                    $tempFiles[] = $tempFile;
                    Log::info('Page extracted successfully', [
                        'page' => $page,
                        'temp_file' => $tempFile
                    ]);
                } else {
                    Log::error('Failed to extract page', [
                        'page' => $page,
                        'return_code' => $pageReturn,
                        'output' => implode("\n", $pageOutput)
                    ]);
                }
            }
            
            // Step 2: Check if all pages were extracted
            if (count($tempFiles) !== count($pages)) {
                // Clean up any temp files that were created
                foreach ($tempFiles as $tempFile) {
                    @unlink($tempFile);
                }
                Log::error('Not all pages could be extracted', [
                    'requested' => count($pages),
                    'extracted' => count($tempFiles)
                ]);
                return false;
            }
            
            // Step 3: Merge all temporary files into the final output
            $command = sprintf(
                '%s -sDEVICE=pdfwrite -dNOPAUSE -dBATCH -dSAFER -sOutputFile=%s %s 2>&1',
                $gsPath,
                escapeshellarg($outputPath),
                implode(' ', array_map('escapeshellarg', $tempFiles))
            );
            
            Log::info('Running GhostScript command', [
                'command' => $command,
                'pages' => $pages
            ]);
            
            exec($command, $output, $returnCode);
            
            // Clean up temp files if they exist
            if (isset($tempFiles)) {
                foreach ($tempFiles as $tempFile) {
                    @unlink($tempFile);
                }
            }
            
            if ($returnCode === 0 && file_exists($outputPath) && filesize($outputPath) > 0) {
                return true;
            }
            
            Log::error('GhostScript command failed', [
                'return_code' => $returnCode,
                'output' => implode("\n", $output)
            ]);
            
            return false;
            
        } catch (\Exception $e) {
            Log::error('Exception in GhostScript split', [
                'error' => $e->getMessage()
            ]);
            return false;
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