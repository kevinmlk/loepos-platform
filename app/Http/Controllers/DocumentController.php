<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use App\Notifications\NewTaskNotification;

use App\Services\DocumentService;
use App\Services\DossierService;
use App\Services\PDFSplitService;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;
use App\Models\Task;

class DocumentController extends Controller
{
    protected $documentService, $dossierService, $pdfSplitService;

    public function __construct(DocumentService $documentService, DossierService $dossierService, PDFSplitService $pdfSplitService)
    {
        $this->documentService = $documentService;
        $this->dossierService = $dossierService;
        $this->pdfSplitService = $pdfSplitService;
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === User::ROLE_ADMIN) {
            // Admin: Get all documents belonging to the user's organization
            $organizationId = $user->organization_id;

            // Get all dossier IDs associated with the organization
            $dossierIds = Dossier::whereHas('user', function ($query) use ($organizationId) {
                $query->where('organization_id', $organizationId);
            })->pluck('id');

            // Get documents associated with the organization's dossiers
            $documents = Document::whereIn('dossier_id', $dossierIds)->paginate(5);
        } elseif ($user->role === User::ROLE_EMPLOYEE) {

            // Employee: Get documents associated with the user's dossiers
            $dossierIds = $user->dossiers()->pluck('id');

            // Get documents associated with the user's dossiers
            $documents = Document::whereIn('dossier_id', $dossierIds)->paginate(5);
        } elseif ($user->role === User::ROLE_SUPERADMIN) {
            $documents = Document::paginate(5);
        } else {
            // Default: No documents for other roles
            $documents = collect(); // Empty collection
        }

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

        // Get user's organization
        $user = Auth::user();

        // Create a new document record with organization_id
        $document = Document::create([
            'dossier_id' => null,
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
        $user = Auth::user();

        // Get all unverified documents for the user's organization
        $documents = Document::where('verified_status', 0)
            ->where('organization_id', $user->organization_id)
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
            'documents.*.dossierId' => 'nullable|exists:dossiers,id',
            'documents.*.pageImages' => 'nullable|array' // Optional page images for fallback
        ]);

        try {
            $user = Auth::user();
            $documentsToVerify = [];

            // Ensure verified documents directory exists
            $this->pdfSplitService->ensureStorageDirectoryExists();

            // Group documents by original document ID for efficient splitting
            $documentsByOriginal = collect($request->documents)->groupBy('originalDocId');

            foreach ($documentsByOriginal as $originalDocId => $splits) {
                // Find the original document
                $originalDocument = Document::find($originalDocId);

                if (!$originalDocument) {
                    continue;
                }

                // Prepare splits configuration
                $splitConfigs = $splits->map(function ($split) {
                    return [
                        'name' => $split['name'],
                        'pages' => $split['pages'],
                        'pageImages' => $split['pageImages'] ?? null
                    ];
                })->toArray();

                // Split the PDF
                $splitFiles = $this->pdfSplitService->splitPDF($originalDocument->file_path, $splitConfigs);

                // Prepare document data for verification
                foreach ($splits as $index => $docData) {
                    $splitFile = $splitFiles[$index] ?? null;

                    if (!$splitFile) {
                        continue;
                    }

                    // Log for debugging
                    Log::info('Preparing document for verification', [
                        'split_file' => $splitFile,
                        'docData' => $docData,
                        'parsed_data' => json_decode($originalDocument->parsed_data, true)
                    ]);

                    $documentsToVerify[] = [
                        'original_document_id' => $originalDocument->id,
                        'file_name' => $docData['name'],
                        'file_path' => $splitFile['path'],
                        'pages' => $docData['pages'],
                        'parsed_data' => json_decode($originalDocument->parsed_data, true),
                        'metadata' => [
                            'pages' => $docData['pages'],
                            'original_file' => $originalDocument->file_name,
                            'split_info' => $splitFile
                        ]
                    ];
                }
            }

            // Store documents in session for verification
            if (!empty($documentsToVerify)) {
                $firstDocument = array_shift($documentsToVerify);
                $request->session()->put('verify_document_data', $firstDocument);
                $request->session()->put('remaining_documents_to_verify', $documentsToVerify);

                return response()->json([
                    'success' => true,
                    'redirect' => route('documents.verify.show')
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error in processQueue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'Failed to process documents'
            ], 500);
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

    /**
     * Create split PDFs from page images (used when FPDI fails due to compression)
     */
    public function createSplitsFromImages(Request $request)
    {
        $request->validate([
            'originalDocId' => 'required|exists:documents,id',
            'pageImages' => 'required|array',
            'splits' => 'required|array',
            'splits.*.name' => 'required|string',
            'splits.*.pages' => 'required|array'
        ]);

        try {
            $originalDocument = Document::find($request->originalDocId);
            $baseFileName = pathinfo($originalDocument->file_name, PATHINFO_FILENAME);

            // Ensure storage directory exists
            $this->pdfSplitService->ensureStorageDirectoryExists();

            // Create PDFs from base64 images
            $splitFiles = $this->pdfSplitService->createPDFsFromBase64Images(
                $request->pageImages,
                $baseFileName,
                $request->splits
            );

            return response()->json([
                'success' => true,
                'splitFiles' => $splitFiles
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating splits from images', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
