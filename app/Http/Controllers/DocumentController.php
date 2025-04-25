<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
// Import Document model
use App\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        // Get all documents
        $documents = Document::all();

        // Return view
        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request) {
        // Validate the request data
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg'
        ]);

        // Get file properties
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('documents', 'public');
        $fileType = $file->getMimeType();

        // Create a new document record
        Document::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $fileType
        ]);

        // Redirect user with a success message
        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully');
    }
}
