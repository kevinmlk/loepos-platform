<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as IlluminateLog;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Services\DocumentService;
use App\Services\UploadService;

use App\Models\Upload;
use App\Models\Document;

class UploadController extends Controller
{
    protected $documentService, $uploadService;

    public function __construct(
        DocumentService $documentService,
        UploadService $uploadService,
    ) {
        $this->documentService = $documentService;
        $this->uploadService = $uploadService;
    }

    // Index method removed - /uploads page no longer exists
    // public function index()
    // {
    //     // Fetch unassigned uploads for the organization
    //     $organizationId = auth()->user()->organization_id;
    //     $unassignedUploads = Upload::whereNull('user_id')
    //         ->where('organization_id', $organizationId)
    //         ->get();

    //     $uploads = Upload::where('user_id', auth()->id())->paginate(5);

    //     return view('uploads.index', [
    //         'uploads' => $uploads,
    //         'unassignedUploads' => $unassignedUploads,
    //     ]);
    // }

    public function create()
    {
        $user = Auth::user();
        
        // Calculate Wachtrij badge count
        $queueCount = $this->calculateQueueCount($user);
        
        // Get the maximum upload size from PHP configuration
        $maxUploadSizeBytes = $this->getMaxUploadSizeBytes();
        $maxUploadSize = $this->formatBytes($maxUploadSizeBytes);
        
        return view('uploads.create', [
            'queueCount' => $queueCount,
            'maxUploadSize' => $maxUploadSize,
            'maxUploadSizeBytes' => $maxUploadSizeBytes
        ]);
    }

    public function store(Request $request)
    {
        // Get max upload size in KB for validation
        $maxKB = min($this->parseSize(ini_get('upload_max_filesize')), $this->parseSize(ini_get('post_max_size'))) / 1024;
        
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg|max:' . $maxKB
        ]);

        try {
            // Get the file properties, extract the documents and store the record
            $file = $request->file('file');
            $fileProperties = $this->documentService->getFileProperties($file);
            $parsedData = $this->uploadService->splitUpload($fileProperties['fullPath']);
            $upload = $this->uploadService->createUploadRecord($fileProperties, $parsedData);

            return response()->json(['redirect' => url('/queue')]);
        } catch (\Exception $e) {
            IlluminateLog::error('Error during file upload: ' . $e->getMessage());
            
            // Use the specific error message if it's a known issue
            $errorMessage = $e->getMessage();
            if (str_contains($errorMessage, 'PDF-bestand lijkt beschadigd') || 
                str_contains($errorMessage, 'bad XRef entry')) {
                $userMessage = 'Het PDF-bestand lijkt beschadigd te zijn. Probeer het bestand opnieuw op te slaan of gebruik een ander bestand.';
            } else {
                $userMessage = 'Er is een fout opgetreden tijdens het uploaden. Probeer het opnieuw.';
            }
            
            // Return JSON error response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'error' => $userMessage
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => $userMessage]);
        }
    }

    // Show method removed - /uploads/{upload} route no longer exists
    // public function show(Upload $upload)
    // {
    //     return view('uploads.show', [
    //         'upload' => $upload,
    //     ]);
    // }

    public function view(Upload $upload)
    {
        // Check if user has access to this upload (through their organization)
        $user = Auth::user();
        
        if ($upload->organization_id !== $user->organization_id) {
            abort(403, 'Unauthorized access to upload');
        }

        // Handle both absolute and relative paths
        $filePath = $upload->file_path;
        
        // If it's an absolute path, try to extract the relative path
        if (str_starts_with($filePath, '/')) {
            // Try to find the file directly
            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);
                $mimeType = mime_content_type($filePath);
                
                return response($content)
                    ->header('Content-Type', $mimeType)
                    ->header('Content-Disposition', 'inline; filename="' . $upload->file_name . '"')
                    ->header('Content-Length', strlen($content));
            }
            
            // Try to extract relative path from storage path
            $storagePath = storage_path('app/public/');
            if (str_starts_with($filePath, $storagePath)) {
                $filePath = str_replace($storagePath, '', $filePath);
            }
        }

        // Check if file exists using Storage facade
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'Upload file not found');
        }

        // Get the file content
        $content = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);

        // Return response with proper headers for inline viewing
        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $upload->file_name . '"')
            ->header('Content-Length', strlen($content));
    }

    /**
     * Calculate the total count for Wachtrij badge
     */
    private function calculateQueueCount($user)
    {
        // Count pending uploads (documents column contains number of documents in each upload)
        $pendingUploadsCount = Upload::where('organization_id', $user->organization_id)
            ->where('status', Upload::STATUS_PENDING)
            ->sum('documents');
        
        // Count pending documents
        $pendingDocumentsCount = Document::where('status', Document::STATUS_PENDING)
            ->whereHas('upload', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->count();
        
        return $pendingUploadsCount + $pendingDocumentsCount;
    }

    /**
     * Get the maximum upload size in bytes
     */
    private function getMaxUploadSizeBytes()
    {
        // Get values from PHP configuration
        $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
        $postMax = $this->parseSize(ini_get('post_max_size'));
        
        // Return the smaller of the two values
        return min($uploadMax, $postMax);
    }
    
    /**
     * Get the maximum upload size from PHP configuration
     */
    private function getMaxUploadSize()
    {
        $maxBytes = $this->getMaxUploadSizeBytes();
        
        // Convert to human readable format
        return $this->formatBytes($maxBytes);
    }

    /**
     * Parse PHP size string to bytes
     */
    private function parseSize($size)
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $size = preg_replace('/[^0-9\.]/', '', $size);
        
        if ($unit) {
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 0) . 'GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 0) . 'MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 0) . 'KB';
        } else {
            return number_format($bytes, 0) . ' bytes';
        }
    }
}
