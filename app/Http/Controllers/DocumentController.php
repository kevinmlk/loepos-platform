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
        return view('documents.index');
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        // Validate
        $attributes = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $mimeType = $file->getMimeType();

        // Generate a unique filename
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        // Store file and ensure it's saved properly
        $storedPath = $file->storeAs('documents', $filename);

        // Double-check full absolute path
        $absolutePath = storage_path("app/{$storedPath}");
        if (!file_exists($absolutePath)) {
            return back()->with('error', 'File storage failed.');
        }

        // Save to DB
        Log::info('Uploading document:', ['file' => $file->getClientOriginalName()]);

        $document = new Document();
        $document->file_name = $file->getClientOriginalName();
        $document->file_path = "documents/{$filePath}";
        $document->mime_type = $file->getClientMimeType();
        $document->save();

        Log::info('Document saved:', ['id' => $document->id]);

        // Send to AI parsing API
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer secret-secret123!',
            ])->attach(
                'file',
                file_get_contents($absolutePath),
                $originalName
            )->post('https://ai.loepos.be/api/process-document');

            if ($response->successful()) {
                $document->parsed_data = json_encode($response->json());
                $document->save();
                return redirect('/documents')->with('success', 'Uploaded and parsed successfully.');
            } else {
                Log::error('AI API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return redirect('/documents')->with('warning', 'Upload succeeded, but parsing failed.');
            }
        } catch (\Exception $e) {
            Log::error('Exception during document parsing', [
                'error' => $e->getMessage(),
            ]);
            return redirect('/documents')->with('error', 'Upload succeeded, but parsing crashed: ' . $e->getMessage());
        }
    }
}
