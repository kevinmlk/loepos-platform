<?php

// app/Services/UploadService.php

namespace App\Services;

use GuzzleHttp\Client;

use App\Models\Upload;

class UploadService
{
    public function splitUpload($filePath) {
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
        $response = $client->post('https://ai.loepos.com/api/page-splitter', [
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

    public function createUploadRecord(array $fileProperties, $parsedData)
    {
        $parsedDataArray = json_decode($parsedData, true);

        return Upload::create([
            'user_id' => auth()->id(),
            'file_name' => $fileProperties['fileName'],
            'file_path' => $fileProperties['fullPath'],
            'parsed_data' => $parsedData,
            'documents' => $parsedDataArray['totalDocuments'],
            'status' => Upload::STATUS_PENDING
        ]);
    }
}
