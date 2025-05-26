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
}
