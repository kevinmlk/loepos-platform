<?php

// app/Services/DocumentService.php

namespace App\Services;

use GuzzleHttp\Client;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\User;

class DocumentService
{
    public function extractText($filePath) {
        // Create a new client and get the API key
        $client = new Client();
        $APIKey = env('OPENAI_API_KEY');

        // Prepare the request payload
        $payload = [
            'multipart' => [
                [
                    'name' => 'file',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath)
                ],
            ],
        ];

        // Send the request to OpenAI API
        $response = $client->post('https://ai.loepos.com/api/process-document', [
            'headers' => [
                'Authorization' => 'Bearer ' . $APIKey,
            ],
            'multipart' => $payload['multipart'],
        ]);

        // Get the JSON response
        $responseData = json_decode($response->getBody(), true);

        // Return the extracted text
        return json_encode($responseData);
    }

    public function getDocuments($user)
    {
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

        return $documents;
    }
}
