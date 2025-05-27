<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\VerifiedDocument;
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
        
        // Get the document data from session or request
        $documentData = $request->session()->get('verify_document_data', []);
        
        if (empty($documentData)) {
            return redirect()->route('documents.queue')->with('error', 'No document data found');
        }
        
        // Log for debugging
        \Log::info('Verify page document data', [
            'documentData' => $documentData
        ]);
        
        // Get clients for the organization
        $clients = Client::where('organization_id', $user->organization_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();
            
        // Get dossiers for the organization
        $dossiers = Dossier::whereHas('clients', function ($query) use ($user) {
                $query->where('organization_id', $user->organization_id);
            })
            ->with('clients')
            ->get();
        
        return view('documents.verify', [
            'documentData' => $documentData,
            'clients' => $clients,
            'dossiers' => $dossiers,
            'documentTypes' => VerifiedDocument::TYPES
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
            'client_id' => 'required|exists:clients,id',
            'dossier_id' => 'required|exists:dossiers,id',
            'type' => 'required|in:' . implode(',', VerifiedDocument::TYPES),
            'sender' => 'required|string|max:255',
            'receiver' => 'required|string|max:255',
            'send_date' => 'nullable|date',
            'receive_date' => 'required|date',
            'due_date' => 'nullable|date',
            'file_path' => 'required|string',
            'file_name' => 'required|string',
            'verified_data' => 'nullable|array'
        ]);
        
        // Verify the client belongs to the user's organization
        $client = Client::where('id', $validated['client_id'])
            ->where('organization_id', $user->organization_id)
            ->firstOrFail();
            
        // Create the verified document
        $verifiedDocument = VerifiedDocument::create([
            'organization_id' => $user->organization_id,
            'client_id' => $validated['client_id'],
            'dossier_id' => $validated['dossier_id'],
            'original_document_id' => $validated['original_document_id'],
            'type' => $validated['type'],
            'file_name' => $validated['file_name'],
            'file_path' => $validated['file_path'],
            'sender' => $validated['sender'],
            'receiver' => $validated['receiver'],
            'send_date' => $validated['send_date'],
            'receive_date' => $validated['receive_date'],
            'due_date' => $validated['due_date'],
            'verified_data' => $validated['verified_data'] ?? [],
            'metadata' => [
                'verified_by' => $user->id,
                'verified_at' => now()->toIso8601String()
            ]
        ]);
        
        // Check if there are more documents to verify in the session
        $remainingDocuments = $request->session()->get('remaining_documents_to_verify', []);
        
        if (!empty($remainingDocuments)) {
            // Get the next document
            $nextDocument = array_shift($remainingDocuments);
            $request->session()->put('remaining_documents_to_verify', $remainingDocuments);
            $request->session()->put('verify_document_data', $nextDocument);
            
            return redirect()->route('documents.verify.show')->with('success', 'Document verified successfully. Please verify the next document.');
        }
        
        // Mark the original document as verified if all splits are done
        $originalDocument = Document::find($validated['original_document_id']);
        if ($originalDocument) {
            $originalDocument->update(['verified_status' => true]);
        }
        
        // Clear session data
        $request->session()->forget(['verify_document_data', 'remaining_documents_to_verify']);
        
        return redirect()->route('documents.queue')->with('success', 'All documents verified successfully!');
    }
}