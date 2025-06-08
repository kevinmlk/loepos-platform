<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;

use App\Models\Document;
use App\Models\Client;
use App\Models\Dossier;
use App\Models\Upload;
use App\Models\FinancialInfo;

class VerifyDocumentController extends Controller
{
    /**
     * Show the verify page for a split document
     */
    public function show(Request $request)
    {
        $user = Auth::user();

        // Get all pending documents for the user's organization
        $pendingDocuments = Document::where('status', Document::STATUS_PENDING)
            ->whereHas('upload', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        if ($pendingDocuments->isEmpty()) {
            return redirect()->route('documents.queue')->with('info', 'No documents to verify');
        }

        // Get the first pending document
        $document = $pendingDocuments->first();

        // Prepare document data in the expected format
        $parsedData = json_decode($document->parsed_data, true) ?? [];

        // If sender/receiver are already in the document, add them to parsed_data for easier access
        if ($document->sender && !data_get($parsedData, 'sender')) {
            $parsedData['sender'] = $document->sender;
        }
        if ($document->receiver && !data_get($parsedData, 'receiver')) {
            $parsedData['receiver'] = $document->receiver;
        }
        if ($document->amount && !data_get($parsedData, 'amount')) {
            $parsedData['amount'] = $document->amount;
        }

        $documentData = [
            'original_document_id' => $document->id,
            'file_name' => $document->file_name,
            'file_path' => $document->file_path,
            'pages' => data_get($parsedData, 'pages', []),
            'parsed_data' => $parsedData
        ];

        // Set progress info
        $progress = [
            'current' => 1,
            'total' => $pendingDocuments->count()
        ];

        // Log for debugging
        \Log::info('Verify page document data', [
            'documentData' => $documentData,
            'parsed_data' => $documentData['parsed_data'],
            'progress' => $progress
        ]);

        // Get dossiers with clients for the organization
        $dossiersWithClients = Dossier::whereHas('user', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->with('client')
            ->get()
            ->map(function($dossier) {
                return [
                    'id' => $dossier->id,
                    'display' => $dossier->client
                        ? "Dossier #{$dossier->id} - {$dossier->client->last_name}, {$dossier->client->first_name}"
                        : "Dossier #{$dossier->id} - (Geen client)",
                    'client_name' => $dossier->client
                        ? "{$dossier->client->last_name} {$dossier->client->first_name}"
                        : null
                ];
            });

        // Calculate Wachtrij badge count
        $queueCount = $this->calculateQueueCount($user);

        return view('documents.verify', [
            'documentData' => $documentData,
            'dossiersWithClients' => $dossiersWithClients,
            'documentTypes' => Document::TYPES,
            'progress' => $progress,
            'queueCount' => $queueCount
        ]);
    }

    /**
     * Process the verified document
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        \Log::info('Verify document store called', [
            'request_data' => $request->all(),
            'user_id' => $user->id
        ]);

        try {
            $validated = $request->validate([
            'original_document_id' => 'required|exists:documents,id',
            'dossier_id' => 'nullable|exists:dossiers,id',
            'create_new' => 'nullable|boolean',
            'new_client' => 'nullable|array',
            'new_client.first_name' => 'required_if:create_new,true|string|max:255',
            'new_client.last_name' => 'required_if:create_new,true|string|max:255',
            'new_client.email' => 'required_if:create_new,true|email|max:255',
            'new_client.phone' => 'nullable|string|max:255',
            'new_client.address' => 'nullable|string|max:255',
            'new_client.city' => 'nullable|string|max:255',
            'new_client.postal_code' => 'nullable|string|max:255',
            'new_client.national_registry_number' => 'nullable|string|max:255',
            'type' => 'required|in:' . implode(',', Document::TYPES),
            'sender' => 'required|string|max:255',
            'receiver' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'send_date' => 'nullable|date',
            'receive_date' => 'required|date',
            'due_date' => 'nullable|date',
            'file_path' => 'required|string',
            'file_name' => 'required|string',
            'verified_data' => 'nullable|array'
        ]);

        // Handle new client and dossier creation if needed
        if ($request->input('create_new') && !empty($validated['new_client'])) {
            // Create new client
            $client = Client::create([
                'first_name' => $validated['new_client']['first_name'],
                'last_name' => $validated['new_client']['last_name'],
                'email' => $validated['new_client']['email'],
                'phone' => !empty($validated['new_client']['phone']) ? $validated['new_client']['phone'] : null,
                'address' => $validated['new_client']['address'] ?? '',
                'city' => $validated['new_client']['city'] ?? '',
                'postal_code' => $validated['new_client']['postal_code'] ?? '',
                'country' => 'België',
                'national_registry_number' => !empty($validated['new_client']['national_registry_number']) ? $validated['new_client']['national_registry_number'] : null
            ]);
            
            // Create financial info with monthly income set to 0
            FinancialInfo::create([
                'client_id' => $client->id,
                'monthly_income' => 0,
                'monthly_expenses' => 0,
                'iban' => '',
                'bank_name' => '',
                'employer' => '',
                'contract' => FinancialInfo::CONTRACT_UNEMPLOYED,
                'education' => FinancialInfo::EDUCATION_PRIMARY
            ]);

            // Create new dossier for the client
            $dossier = Dossier::create([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'type' => Dossier::TYPE_DEBT_MEDIATION, // Default type
                'status' => Dossier::STATUS_IN_PROCESS
            ]);

            $validated['dossier_id'] = $dossier->id;
        }

        // Verify dossier belongs to user's organization
        if ($validated['dossier_id']) {
            $dossier = Dossier::whereHas('user', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })->find($validated['dossier_id']);

            if (!$dossier) {
                return back()->withErrors(['dossier_id' => 'Invalid dossier selection']);
            }
        }

        // Update the existing document
        $document = Document::find($validated['original_document_id']);
        if ($document) {
            \Log::info('Updating document', [
                'document_id' => $document->id,
                'old_status' => $document->status,
                'new_status' => Document::STATUS_VERIFIED,
                'dossier_id' => $validated['dossier_id']
            ]);

            // Generate new file name based on available data
            $extension = pathinfo($document->file_name, PATHINFO_EXTENSION);
            $newFileName = $document->file_name; // Default to current name

            // Check for description or invoice number in verified data
            $description = $validated['verified_data']['description'] ?? null;
            $invoiceNumber = $validated['verified_data']['invoiceNumber'] ?? null;

            if ($description && $invoiceNumber) {
                // Use format: InvoiceNumber_Description
                $newFileName = $invoiceNumber . '_' . $description;
            } elseif ($description) {
                // Use just description if no invoice number
                $newFileName = $description;
            } elseif ($invoiceNumber) {
                // Use just invoice number if no description
                $newFileName = $invoiceNumber;
            }

            // Clean the filename - remove invalid characters
            if ($newFileName !== $document->file_name) {
                $newFileName = preg_replace('/[^a-zA-Z0-9_\-\s]/', '', $newFileName);
                $newFileName = preg_replace('/\s+/', '_', trim($newFileName));
                $newFileName = $newFileName . '.' . $extension;
            }

            $document->update([
                'dossier_id' => $validated['dossier_id'],
                'type' => $validated['type'],
                'sender' => $validated['sender'],
                'receiver' => $validated['receiver'],
                'status' => Document::STATUS_VERIFIED,
                'amount' => $validated['amount'] ?? ($validated['verified_data']['invoiceAmount'] ?? $document->amount),
                'file_name' => $newFileName
            ]);

            if ($document->dossier_id) {
                Dossier::where('id', $document->dossier_id)->update(['updated_at' => now()]);
            }

            // Update parsed_data with verified data
            $parsedData = json_decode($document->parsed_data, true) ?? [];
            $parsedData['verified_data'] = $validated['verified_data'] ?? [];
            $parsedData['verified_by'] = $user->id;
            $parsedData['verified_at'] = now()->toIso8601String();

            // Store date fields in parsed_data
            if (isset($validated['send_date'])) {
                $parsedData['send_date'] = $validated['send_date'];
            }
            if (isset($validated['receive_date'])) {
                $parsedData['receive_date'] = $validated['receive_date'];
            }
            if (isset($validated['due_date'])) {
                $parsedData['due_date'] = $validated['due_date'];
            }

            $document->parsed_data = json_encode($parsedData);
            $document->save();
        }

        // Check if there are more pending documents
        $remainingPendingCount = Document::where('status', Document::STATUS_PENDING)
            ->whereHas('upload', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->where('id', '!=', $validated['original_document_id'])
            ->count();

        if ($remainingPendingCount > 0) {
            return redirect()->route('queue.verify')->with('success', 'Document geverifieerd. Nog ' . $remainingPendingCount . ' documenten te verifiëren.');
        }

        return redirect()->route('documents.queue')->with('success', 'Alle documenten zijn succesvol geverifieerd!');

        } catch (\Exception $e) {
            \Log::error('Error in verify document store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Er is een fout opgetreden bij het verifiëren van het document: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a document
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        // Find the document and verify it belongs to user's organization
        $document = Document::where('id', $id)
            ->whereHas('upload', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document niet gevonden'
            ], 404);
        }

        // Update document status to rejected
        $document->update([
            'status' => Document::STATUS_REJECTED
        ]);

        // Check for remaining pending documents
        $remainingPendingCount = Document::where('status', Document::STATUS_PENDING)
            ->whereHas('upload', function($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Document is weggegooid',
            'redirect' => $remainingPendingCount > 0
                ? route('queue.verify')
                : route('documents.queue')
        ]);
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
     * Check if parsed data has the expected format
     */
    private function isValidParsedData($parsedData)
    {
        if (empty($parsedData)) {
            return false;
        }

        // Check for expected structure patterns
        $hasExpectedStructure =
            // Check for standard format
            (isset($parsedData['data']) && is_array($parsedData['data'])) ||
            // Check for content.documents format
            (isset($parsedData['content']['documents']) && is_array($parsedData['content']['documents'])) ||
            // Check for documents array format
            (isset($parsedData['documents']) && is_array($parsedData['documents'])) ||
            // Check for direct fields
            (isset($parsedData['sender']) || isset($parsedData['receiver']) || isset($parsedData['documentType']));

        // Also check if we have at least some meaningful data
        $hasMeaningfulData = false;

        // Check various possible paths for sender/receiver
        $senderPaths = [
            'data.sender', 'sender', 'from', 'content.documents.0.sender',
            'documents.0.sender', 'data.sender.name', 'sender.name'
        ];

        $receiverPaths = [
            'data.receiver', 'receiver', 'to', 'content.documents.0.receiver',
            'documents.0.receiver', 'data.receiver.name', 'receiver.name'
        ];

        foreach ($senderPaths as $path) {
            if (data_get($parsedData, $path)) {
                $hasMeaningfulData = true;
                break;
            }
        }

        if (!$hasMeaningfulData) {
            foreach ($receiverPaths as $path) {
                if (data_get($parsedData, $path)) {
                    $hasMeaningfulData = true;
                    break;
                }
            }
        }

        return $hasExpectedStructure && $hasMeaningfulData;
    }

    /**
     * Reanalyze document using AI API
     */
    private function reanalyzeDocument($document)
    {
        try {
            // Get the full path to the file
            $fullPath = Storage::disk('public')->path($document->file_path);

            if (!file_exists($fullPath)) {
                Log::error('File not found for reanalysis', ['path' => $fullPath]);
                return [];
            }

            $client = new GuzzleClient();
            $APIKey = env('OPENAI_API_KEY');

            // Prepare the request payload using multipart form data
            $response = $client->post('https://ai.loepos.be/api/analyze', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $APIKey,
                ],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => fopen($fullPath, 'r'),
                        'filename' => basename($fullPath)
                    ],
                ],
                'timeout' => 30
            ]);

            // Get the JSON response
            $responseData = json_decode($response->getBody(), true);

            Log::info('Document reanalyzed successfully', [
                'document_id' => $document->id,
                'file' => $document->file_path,
                'response' => $responseData
            ]);

            return $responseData ?? [];

        } catch (\Exception $e) {
            Log::error('Error reanalyzing document', [
                'document_id' => $document->id,
                'file' => $document->file_path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    /**
     * Extract document type from parsed data
     */
    private function detectDocumentType($parsedData)
    {
        if (empty($parsedData)) return null;

        // Check for document type in various locations
        $possiblePaths = [
            'documentType',
            'type',
            'document_type',
            'data.documentDetails.documentType',
            'content.documentType',
            'content.type',
            'documents.0.documentDetails.documentType',
            'documents.0.type'
        ];

        foreach ($possiblePaths as $path) {
            $value = data_get($parsedData, $path);
            if ($value && in_array(strtolower($value), array_map('strtolower', Document::TYPES))) {
                return strtolower($value);
            }
        }

        // Try to detect based on content
        $content = json_encode($parsedData);
        if (stripos($content, 'invoice') !== false || stripos($content, 'factuur') !== false) {
            return Document::TYPE_INVOICE;
        }
        if (stripos($content, 'reminder') !== false || stripos($content, 'herinnering') !== false) {
            return Document::TYPE_REMINDER;
        }
        if (stripos($content, 'agreement') !== false || stripos($content, 'overeenkomst') !== false) {
            return Document::TYPE_AGREEMENT;
        }

        return null;
    }

    /**
     * Extract sender from parsed data
     */
    private function extractSender($parsedData)
    {
        if (empty($parsedData)) return null;

        $possiblePaths = [
            'sender',
            'sender.name',
            'from',
            'afzender',
            'creditor',
            'data.sender',
            'data.sender.name',
            'content.sender',
            'content.sender.name',
            'documents.0.sender.name',
            'documents.0.sender',
            'content.documents.0.sender.name',
            'content.documents.0.sender'
        ];

        foreach ($possiblePaths as $path) {
            $value = data_get($parsedData, $path);
            if ($value && is_string($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Extract receiver from parsed data
     */
    private function extractReceiver($parsedData)
    {
        if (empty($parsedData)) return null;

        $possiblePaths = [
            'receiver',
            'receiver.name',
            'to',
            'recipient',
            'ontvanger',
            'debtor',
            'clientName',
            'data.receiver',
            'data.receiver.name',
            'content.receiver',
            'content.receiver.name',
            'documents.0.receiver.name',
            'documents.0.receiver',
            'content.documents.0.receiver.name',
            'content.documents.0.receiver'
        ];

        foreach ($possiblePaths as $path) {
            $value = data_get($parsedData, $path);
            if ($value && is_string($value)) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Extract amount from parsed data
     */
    private function extractAmount($parsedData)
    {
        if (empty($parsedData)) return null;

        $possiblePaths = [
            'amount',
            'total',
            'totalAmount',
            'invoiceAmount',
            'bedrag',
            'totaal',
            'data.documentDetails.invoiceAmount',
            'content.amount',
            'content.total',
            'documents.0.documentDetails.invoiceAmount',
            'documents.0.amount',
            'content.documents.0.documentDetails.invoiceAmount'
        ];

        foreach ($possiblePaths as $path) {
            $value = data_get($parsedData, $path);
            if ($value !== null) {
                // Try to convert to float
                if (is_numeric($value)) {
                    return (float) $value;
                }
                // Handle formatted amounts like "€ 1.234,56"
                if (is_string($value)) {
                    $cleaned = preg_replace('/[^0-9,.-]/', '', $value);
                    $cleaned = str_replace(',', '.', $cleaned);
                    if (is_numeric($cleaned)) {
                        return (float) $cleaned;
                    }
                }
            }
        }

        return null;
    }
}
