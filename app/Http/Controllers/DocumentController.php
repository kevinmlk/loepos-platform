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
use App\Services\TaskService;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;
use App\Models\Task;
use App\Models\Upload;

class DocumentController extends Controller
{
    protected $documentService, $dossierService, $pdfSplitService, $taskService;

    public function __construct(
        DocumentService $documentService,
        DossierService $dossierService,
        PDFSplitService $pdfSplitService,
        TaskService $taskService
    ) {
        $this->documentService = $documentService;
        $this->dossierService = $dossierService;
        $this->pdfSplitService = $pdfSplitService;
        $this->taskService = $taskService;
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
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg'
        ]);

        $file = $request->file('file');

        // Get the file properties and extract the text
        $fileProperties = $this->documentService->getFileProperties($file);
        $parsedData = $this->documentService->extractText($fileProperties['fullPath']);

        // Decode the parsed data to extract client information-This is not being used?
        $parsedDataArray = json_decode($parsedData, true);

        // Create document and task records
        $document = $this->documentService->createDocumentRecord($fileProperties, $parsedData);
        $task = $this->taskService->createTaskForDocument($document);

        // Notify the user
        $user = Auth::user();
        $user->notify(new NewTaskNotification($task));

        return redirect('/documents');
    }

    public function queue()
    {
        $user = Auth::user();

        // Get all pending uploads for the user's organization that have parsed_data
        $uploads = Upload::where('organization_id', $user->organization_id)
            ->where('status', 'pending')  // Using string directly to match database
            ->whereNotNull('parsed_data')
            ->get();
            
        Log::info('Queue page - Found uploads:', [
            'count' => $uploads->count(),
            'user_org_id' => $user->organization_id,
            'uploads' => $uploads->map(function($u) {
                return [
                    'id' => $u->id,
                    'status' => $u->status,
                    'file_name' => $u->file_name,
                    'has_parsed_data' => !empty($u->parsed_data)
                ];
            })->toArray()
        ]);

        // Transform uploads to document-like structure for the view
        $documents = $uploads->map(function($upload) {
            $parsedData = json_decode($upload->parsed_data, true);
            
            return (object) [
                'id' => $upload->id,
                'upload_id' => $upload->id,
                'file_name' => $upload->file_name,
                'file_path' => $upload->file_path,
                'parsed_data' => $upload->parsed_data,
                'created_at' => $upload->created_at,
                'upload' => (object) [
                    'id' => $upload->id,
                    'file_name' => $upload->file_name,
                    'file_path' => $upload->file_path,
                    'created_at' => $upload->created_at
                ],
                'status' => 'pending',
                'dossier' => null
            ];
        });

        return view('documents.queue', [
            'documents' => $documents
        ]);
    }


    public function processQueue(Request $request)
    {
        $request->validate([
            'documents' => 'required|array',
            'documents.*.originalDocId' => 'required|exists:uploads,id',
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

            // Group documents by original upload ID for efficient splitting
            $documentsByOriginal = collect($request->documents)->groupBy('originalDocId');

            foreach ($documentsByOriginal as $originalUploadId => $splits) {
                // Find the original upload
                $originalUpload = Upload::find($originalUploadId);

                if (!$originalUpload) {
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
                $splitFiles = $this->pdfSplitService->splitPDF($originalUpload->file_path, $splitConfigs);

                // Prepare document data for verification
                foreach ($splits as $index => $docData) {
                    $splitFile = $splitFiles[$index] ?? null;

                    if (!$splitFile) {
                        continue;
                    }

                    // Create document record from the split
                    $document = new Document();
                    $document->upload_id = $originalUpload->id;
                    $document->file_name = $docData['name'];
                    $document->file_path = $splitFile['path'];
                    $document->type = Document::TYPE_INVOICE; // Default type, can be updated later
                    $document->status = Document::STATUS_PENDING;
                    $document->sender = ''; // To be filled during verification
                    $document->receiver = ''; // To be filled during verification
                    $document->parsed_data = json_encode([
                        'pages' => $docData['pages'],
                        'source' => 'upload',
                        'upload_id' => $originalUpload->id,
                        'original_file' => $originalUpload->file_name,
                        'split_info' => $splitFile
                    ]);
                    $document->save();

                    // Create a task for this document
                    $task = $this->taskService->createTaskForDocument($document);

                    // Log for debugging
                    Log::info('Created document from upload split', [
                        'upload_id' => $originalUpload->id,
                        'document_id' => $document->id,
                        'split_file' => $splitFile,
                        'docData' => $docData
                    ]);

                    $documentsToVerify[] = [
                        'original_document_id' => $document->id,
                        'file_name' => $docData['name'],
                        'file_path' => $splitFile['path'],
                        'pages' => $docData['pages'],
                        'parsed_data' => json_decode($document->parsed_data, true),
                        'metadata' => [
                            'pages' => $docData['pages'],
                            'original_file' => $originalUpload->file_name,
                            'split_info' => $splitFile
                        ]
                    ];
                }
                
                // Update upload status to verified after processing
                $originalUpload->status = Upload::STATUS_VERIFIED;
                $originalUpload->save();
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
