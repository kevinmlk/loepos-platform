<?php

// app/Services/DocumentService.php

namespace App\Services;

use GuzzleHttp\Client;

class DocumentService {

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
}
