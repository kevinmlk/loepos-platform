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
        
        // Calculate AI queue badge count
        $queueCount = $this->calculateQueueCount($user);
        
        return view('uploads.create', [
            'queueCount' => $queueCount
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg'
        ]);

        try {
            // Get the file properties, extract the documents and store the record
            $file = $request->file('file');
            $fileProperties = $this->documentService->getFileProperties($file);
            $parsedData = $this->uploadService->splitUpload($fileProperties['fullPath']);
            $upload = $this->uploadService->createUploadRecord($fileProperties, $parsedData);

            return response()->json(['redirect' => url('/uploads/' . $upload->id)]);
        } catch (\Exception $e) {
            IlluminateLog::error('Error during file upload: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'An error occurred during the upload process. Please try again.']);
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
     * Calculate the total count for AI queue badge
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
}
