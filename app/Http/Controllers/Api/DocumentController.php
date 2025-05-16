<?php
// app\Http\Controllers\Api\DocumentController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Services\DocumentService;
use App\Services\DossierService;

use App\Models\Document;
use App\Models\Dossier;

class DocumentController extends Controller
{
    protected $documentService, $dossierService;

    public function __construct(DocumentService $documentService, DossierService $dossierService)
    {
        $this->documentService = $documentService;
        $this->dossierService = $dossierService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data for API request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,png,jpg',
            'sender_email' => 'required|email',
            'receiver_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Store sender email from request
        $senderEmail = $request->input('sender_email');

        // Check in the db if the sender email matches a client or user
        if (!$this->isSenderEmailAllowed($senderEmail)) {
            return response()->json(['error' => 'Unauthorized sender email'], 403);
        }

        // Get file properties
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('documents', 'public');

        // Extract text using OpenAI API
        $fullPath = Storage::disk('public')->path($filePath);
        $parsedData = $this->documentService->extractText($fullPath);

        // Decode the parsed data to extract client information
        $parsedDataArray = json_decode($parsedData, true);

        // Determine the dossier_id using DocumentService
        $dossierId = $this->dossierService->determineDossierId($parsedDataArray);

        // Create a new document record
        Document::create([
            'dossier_id' => $dossierId ?? 1,
            'type' => Document::TYPE_INVOICE,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'parsed_data' => $parsedData,
        ]);

        // Return a JSON response with success message
        return response()->json([
            'message' => 'Document uploaded successfully',
            'file_name' => $fileName,
            'file_path' => $filePath,
            'parsed_data' => $parsedData,
            'sender_email' => $request->input('sender_email'),
            'receiver_email' => $request->input('receiver_email')
        ], 201);
    }

    /**
    * Check if the sender email is allowed.
    *
    * @param string $senderEmail
    * @return bool
    */

    private function isSenderEmailAllowed(string $senderEmail): bool
    {
        return Dossier::whereHas('client', function ($query) use ($senderEmail) {
            $query->where('email', $senderEmail);
        })->orWhereHas('user', function ($query) use ($senderEmail) {
            $query->where('email', $senderEmail);
        })->exists();
    }

}
