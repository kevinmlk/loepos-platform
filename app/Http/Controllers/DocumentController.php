<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use App\Services\DocumentService;
use App\Services\DossierService;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;

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

        // Return view
        return view('documents.index', [
            'documents' => $documents
        ]);
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request) {
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
        Document::create([
            'dossier_id' => $dossierId ?? 1,
            'type' => Document::TYPE_INVOICE,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'parsed_data' => $parsedData,
        ]);

        // Redirect user with a success message
        return redirect('/documents');
    }
}
