<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Notifications\NewTaskNotification;

use App\Services\DocumentService;
use App\Services\DossierService;

use App\Models\Document;
use App\Models\Task;

class DocumentController extends Controller
{
    protected $documentService, $dossierService;

    public function __construct(DocumentService $documentService, DossierService $dossierService)
    {
        $this->documentService = $documentService;
        $this->dossierService = $dossierService;
    }

    public function index()
    {
        $user = Auth::user();

        $documents = $this->documentService->getDocuments($user);

        return view('documents.index', [
            'documents' => $documents
        ]);
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
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
        $parsedData = $this->documentService->extractText($fullPath);

        // Decode the parsed data to extract client information
        $parsedDataArray = json_decode($parsedData, true);

        // Determine the dossier_id using DocumentService
        $dossierId = $this->dossierService->determineDossierId($parsedDataArray);

        // Create a new document record
        $document = Document::create([
            'dossier_id' => $dossierId ?? 1,
            'type' => Document::TYPE_INVOICE,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'parsed_data' => $parsedData,
        ]);

        // Create a new task
        $task = $document->tasks()->create([
            'description' => 'Review the uploaded document.',
            'status' => Task::STATUS_PENDING,
            'urgency' => Task::URGENCY_MEDIUM,
            'due_date' => now()->addDays(3)
        ]);

        // Notify the user
        $user = Auth::user();
        $user->notify(new NewTaskNotification($task));

        // Redirect user with a success message
        return redirect('/documents');
    }

    public function queue()
    {
        // Get all unverified documents
        $documents = Document::where('verified_status', 0)
            ->with('dossier')
            ->get();

        return view('documents.queue', [
            'documents' => $documents
        ]);
    }

    public function processQueue(Request $request)
    {
        $request->validate([
            'documents' => 'required|array',
            'documents.*.originalDocId' => 'required|exists:documents,id',
            'documents.*.name' => 'required|string',
            'documents.*.pages' => 'required|array',
            'documents.*.dossierId' => 'nullable|exists:dossiers,id'
        ]);

        try {
            foreach ($request->documents as $docData) {
                // Find the original document
                $originalDocument = Document::find($docData['originalDocId']);
                
                if (!$originalDocument) {
                    continue;
                }

                // Update the original document or create new split documents
                if (count($request->documents) === 1 && $docData['originalDocId'] === $originalDocument->id) {
                    // If only one document and it's the original, just update it
                    $originalDocument->update([
                        'dossier_id' => $docData['dossierId'] ?? $originalDocument->dossier_id,
                        'verified_status' => 1
                    ]);
                } else {
                    // Create new documents for splits
                    // This is a simplified version - in production, we'd need to actually split the PDF
                    Document::create([
                        'dossier_id' => $docData['dossierId'] ?? $originalDocument->dossier_id,
                        'type' => $originalDocument->type,
                        'file_name' => $docData['name'],
                        'file_path' => $originalDocument->file_path, // In production, create actual split PDFs
                        'parsed_data' => json_encode([
                            'pages' => $docData['pages'],
                            'original_document_id' => $originalDocument->id
                        ]),
                        'verified_status' => 1
                    ]);
                }
            }

            // Mark original documents as processed
            $processedIds = collect($request->documents)->pluck('originalDocId')->unique();
            Document::whereIn('id', $processedIds)->update(['verified_status' => 1]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function view(Document $document)
    {
        // Check if user has access to this document (through their organization)
        $user = Auth::user();
        
        // We should add additional access checks here
        // For example, check if the user's organization matches the document's dossier organization
        
        // Check if file exists
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document not found');
        }
        
        // Get the file content
        $content = Storage::disk('public')->get($document->file_path);
        $mimeType = Storage::disk('public')->mimeType($document->file_path);
        
        // Return response with proper headers for inline viewing
        return response($content)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->file_name . '"')
            ->header('Content-Length', strlen($content));
    }

    public function download(Document $document)
    {
        // Check if user has access to this document (through their organization)
        $user = Auth::user();
        
        // We should add additional access checks here
        // For example, check if the user's organization matches the document's dossier organization
        
        // Check if file exists
        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'Document not found');
        }
        
        // Force download the file
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }
}
