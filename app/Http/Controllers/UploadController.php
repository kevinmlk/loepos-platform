<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Upload;

use App\Services\DocumentService;
use App\Services\UploadService;

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

    public function create()
    {
        return view('uploads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg'
        ]);

        // Get the file properties and extract the text
        $file = $request->file('file');
        $fileProperties = $this->documentService->getFileProperties($file);
        $parsedData = $this->uploadService->splitUpload($fileProperties['fullPath']);

        // Decode the parsed data to extract client information-This is not being used?
        $parsedDataArray = json_decode($parsedData, true);

        // Create upload
        Upload::create([
            'file_name' => $fileProperties['fileName'],
            'file_path' => $fileProperties['fullPath'],
            'parsed_data' => $parsedData,
            'documents' => $parsedDataArray['totalDocuments'],
            'status' => Upload::STATUS_UPLOADED
        ]);

        return redirect('/documents');
    }
}
