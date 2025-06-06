<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as IlluminateLog;

use App\Services\DocumentService;
use App\Services\UploadService;

use App\Models\Upload;

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

    public function index()
    {
        // Fetch unassigned uploads for the organization
        $organizationId = auth()->user()->organization_id;
        $unassignedUploads = Upload::whereNull('user_id')
            ->where('organization_id', $organizationId)
            ->get();

        $uploads = Upload::where('user_id', auth()->id())->paginate(5);

        return view('uploads.index', [
            'uploads' => $uploads,
            'unassignedUploads' => $unassignedUploads,
        ]);
    }

    public function create()
    {
        return view('uploads.create');
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

    public function show(Upload $upload)
    {
        return view('uploads.show', [
            'upload' => $upload,
        ]);
    }
}
