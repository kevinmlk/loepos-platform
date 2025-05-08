<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
// Import Document model
use App\Models\Document;

class DocumentController extends Controller
{
    public function index()
    {
        // Get all documents
        $documents = Document::paginate(5);

        // Return view
        return view('documents.index', [
            'documents' => $documents
        ]);
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

        // Extract text using OpenAI API
        $fullPath = Storage::disk('public')->path($filePath);
        $parsedData = $this->extractText($fullPath);

        // Create a new document record
        Document::create([
            'dossier_id' => 1,
            'type' => Document::TYPE_BILL,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'parsed_data' => $parsedData,
        ]);

        // Redirect user with a success message
        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully');
    }

    private function extractText($filePath) {
        // Create a new client and get the API key
        $client = new Client();
        $APIKey = env('OPENAI_API_KEY');

        // Prepare the request payload
        $payload = [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath)
                ],
            ],
        ];

        // Send the request to OpenAI API
        $response = $client->post('https://ai.loepos.com/api/process-document', [
            'headers' => [
                'Authorization' => 'Bearer ' . $APIKey,
            ],
            'multipart' => $payload['multipart'],
        ]);

        // Get the JSON response
        $responseData = json_decode($response->getBody(), true);

        // Return the extracted text
        return json_encode($responseData);
    }
}
