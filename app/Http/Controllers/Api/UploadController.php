<?php
// app\Http\Controllers\Api\UploadController.php
namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\DocumentService;
use App\Services\UploadService;

use App\Models\Dossier;
use App\Models\Client;
use App\Models\User;

class UploadController extends Controller
{
    protected $documentService, $uploadService;

    public function __construct(
        DocumentService $documentService,
        UploadService $uploadService
    ) {
        $this->documentService = $documentService;
        $this->uploadService = $uploadService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data for API request
        $validator = $this->validateUpload($request);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check in the db if the sender email matches a client or user and get organization
        $senderEmail = $request->input('sender_email');
        if (!$this->isSenderEmailAllowed($senderEmail)) {
            return response()->json(['error' => 'Unauthorized sender email'], 403);
        }

        try {
            // Get the file properties, extract the documents and store the record
            $file = $request->file('file');
            $fileProperties = $this->documentService->getFileProperties($file);
            $parsedData = $this->uploadService->splitUpload($fileProperties['fullPath']);
            $upload = $this->uploadService->createUploadRecord($fileProperties, $parsedData);

            return $this->jsonResponse($fileProperties, $parsedData, $request);
        } catch (\Exception $e) {
            IlluminateLog::error('Error during file upload: ' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'An error occurred during the upload process. Please try again.']);
        }
    }

    /**
    * Get organization ID from sender email.
    *
    * @param string $senderEmail
    * @return int|null
    */
    private function getOrganizationFromSenderEmail(string $senderEmail): ?int
    {
        // Check if email belongs to a client
        $client = Client::where('email', $senderEmail)->first();
        if ($client) {
            return $client->organization_id;
        }

        // Check if email belongs to a user
        $user = User::where('email', $senderEmail)->first();
        if ($user) {
            return $user->organization_id;
        }

        return null;
    }

    /**
    * Check if sender email is allowed.
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

    private function validateUpload($request)
    {
        return Validator::make($request->all(), [
            'file' => 'required|file|mimes:pdf,png,jpg',
            'sender_email' => 'required|email',
            'receiver_email' => 'required|email',
        ]);
    }

    private function jsonResponse($fileProperties, $parsedData, $request)
    {
        return response()->json([
            'message' => 'File uploaded successfully',
            'file_name' => $fileProperties['fileName'],
            'file_path' => $fileProperties['fullPath'],
            'parsed_data' => $parsedData,
            'sender_email' => $request->input('sender_email'),
            'receiver_email' => $request->input('receiver_email')
        ], 201);
    }

}
