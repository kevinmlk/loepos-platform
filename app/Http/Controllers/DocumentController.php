<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

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
        Log::info('ProcessQueue called', [
            'documents_count' => count($request->input('documents', [])),
            'first_document' => $request->input('documents.0')
        ]);
        
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
            $processedCount = 0;
            $totalCount = collect($request->documents)->count();

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
                try {
                    $splitFiles = $this->pdfSplitService->splitPDF($originalUpload->file_path, $splitConfigs);
                } catch (\Exception $e) {
                    Log::error('Error splitting PDF', [
                        'upload_id' => $originalUploadId,
                        'file_path' => $originalUpload->file_path,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }

                // Process each split document
                foreach ($splits as $index => $docData) {
                    $splitFile = $splitFiles[$index] ?? null;

                    if (!$splitFile) {
                        continue;
                    }

                    // Make API call to analyze the document
                    $parsedData = $this->analyzeDocument($splitFile['path']);

                    // Create document record from the split
                    $document = new Document();
                    $document->upload_id = $originalUpload->id;
                    $document->file_name = $docData['name'];
                    $document->file_path = $splitFile['path'];
                    $document->type = $this->detectDocumentType($parsedData) ?? Document::TYPE_INVOICE;
                    $document->status = Document::STATUS_PENDING;
                    $document->sender = $this->extractSender($parsedData) ?? '';
                    $document->receiver = $this->extractReceiver($parsedData) ?? '';
                    $document->parsed_data = json_encode(array_merge($parsedData, [
                        'pages' => $docData['pages'],
                        'source' => 'upload',
                        'upload_id' => $originalUpload->id,
                        'original_file' => $originalUpload->file_name,
                        'split_info' => $splitFile
                    ]));
                    
                    // Set amount if available
                    $amount = $this->extractAmount($parsedData);
                    if ($amount !== null) {
                        $document->amount = $amount;
                    }
                    
                    $document->save();
                    $processedCount++;

                    // Create a task for this document
                    $task = $this->taskService->createTaskForDocument($document);

                    // Log for debugging
                    Log::info('Created document from upload split', [
                        'upload_id' => $originalUpload->id,
                        'document_id' => $document->id,
                        'split_file' => $splitFile,
                        'docData' => $docData,
                        'parsed_data' => $parsedData
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
                            'split_info' => $splitFile,
                            'processed_count' => $processedCount,
                            'total_count' => $totalCount
                        ]
                    ];
                }
                
                // Update upload status to verified after processing
                $originalUpload->status = Upload::STATUS_VERIFIED;
                $originalUpload->save();
            }

            // Store documents in session for verification
            if (!empty($documentsToVerify)) {
                Log::info('Storing documents in session for verification', [
                    'count' => count($documentsToVerify),
                    'first_doc' => $documentsToVerify[0] ?? null
                ]);
                
                $request->session()->put('documents_to_verify', $documentsToVerify);
                $request->session()->put('verify_progress', [
                    'current' => 1,
                    'total' => count($documentsToVerify)
                ]);

                return response()->json([
                    'success' => true,
                    'redirect' => route('queue.verify'),
                    'processed' => $processedCount,
                    'total' => $totalCount
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

    /**
     * Analyze document using AI API
     */
    private function analyzeDocument($filePath)
    {
        try {
            // Get the full path to the file
            $fullPath = Storage::disk('public')->path($filePath);
            
            if (!file_exists($fullPath)) {
                Log::error('File not found for analysis', ['path' => $fullPath]);
                return [];
            }
            
            $client = new Client();
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
            
            Log::info('Document analyzed successfully', [
                'file' => $filePath,
                'response' => $responseData
            ]);
            
            return $responseData ?? [];
            
        } catch (\Exception $e) {
            Log::error('Error analyzing document', [
                'file' => $filePath,
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
            'content.sender',
            'content.sender.name',
            'documents.0.sender.name',
            'documents.0.sender'
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
            'content.receiver',
            'content.receiver.name',
            'documents.0.receiver.name',
            'documents.0.receiver'
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
            'content.amount',
            'content.total',
            'documents.0.documentDetails.invoiceAmount',
            'documents.0.amount'
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
