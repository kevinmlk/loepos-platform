<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function index($upload)
    {
        return view('uploads.index', [
            'upload' => $upload
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

        // Get the file properties, extract the documents and store the record
        $file = $request->file('file');
        $fileProperties = $this->documentService->getFileProperties($file);
        $parsedData = $this->uploadService->splitUpload($fileProperties['fullPath']);
        $upload = $this->uploadService->createUploadRecord($fileProperties, $parsedData);

        return redirect()->route('uploads.show', $upload);
    }

    public function show(Upload $upload)
    {
        return view('uploads.show', [
            'upload' => $upload,
        ]);
    }
}
