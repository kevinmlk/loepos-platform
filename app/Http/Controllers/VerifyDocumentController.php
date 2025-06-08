<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Document;
use App\Models\Client;
use App\Models\Dossier;

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

        return view('documents.verify', [
            'documentData' => $documentData,
            'dossiersWithClients' => $dossiersWithClients,
            'documentTypes' => Document::TYPES,
            'progress' => $progress
        ]);
    }

    /**
     * Process the verified document
     */
    public function store(Request $request)
    {
        $user = Auth::user();

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
                'phone' => $validated['new_client']['phone'] ?? '',
                'address' => $validated['new_client']['address'] ?? '',
                'city' => $validated['new_client']['city'] ?? '',
                'postal_code' => $validated['new_client']['postal_code'] ?? '',
                'country' => 'België',
                'national_registry_number' => $validated['new_client']['national_registry_number'] ?? ''
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
            $document->update([
                'dossier_id' => $validated['dossier_id'],
                'type' => $validated['type'],
                'sender' => $validated['sender'],
                'receiver' => $validated['receiver'],
                'status' => Document::STATUS_VERIFIED,
                'amount' => $validated['amount'] ?? ($validated['verified_data']['invoiceAmount'] ?? $document->amount)
            ]);

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
}
