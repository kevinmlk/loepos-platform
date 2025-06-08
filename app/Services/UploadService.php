<?php

// app/Services/UploadService.php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;

use App\Models\Upload;

class UploadService
{
    public function splitUpload($filePath) {
        $client = new Client();
        $APIKey = env('OPENAI_API_KEY');

        try {
            // Validate that the file exists and is readable
            if (!file_exists($filePath) || !is_readable($filePath)) {
                throw new \Exception('Het bestand kon niet worden gelezen.');
            }
            
            // Check if it's a valid PDF by reading the header
            $fileHandle = fopen($filePath, 'r');
            $header = fread($fileHandle, 5);
            fclose($fileHandle);
            
            if ($header !== '%PDF-') {
                throw new \Exception('Het bestand is geen geldig PDF-bestand.');
            }
            
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
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            // Handle 500 errors from the AI service
            $response = $e->getResponse();
            $responseBody = json_decode($response->getBody()->getContents(), true);
            
            // Check if it's a bad XRef error
            if (isset($responseBody['details']) && str_contains($responseBody['details'], 'bad XRef entry')) {
                throw new \Exception('Het PDF-bestand lijkt beschadigd te zijn. Probeer het bestand opnieuw op te slaan of gebruik een ander bestand.');
            }
            
            // Re-throw with original error message
            throw new \Exception('Er is een fout opgetreden bij het verwerken van het bestand: ' . ($responseBody['error'] ?? 'Onbekende fout'));
        } catch (\Exception $e) {
            // Re-throw any other exceptions
            throw $e;
        }
    }

    public function createUploadRecord(array $fileProperties, $parsedData, $organizationId = null)
    {
        $parsedDataArray = json_decode($parsedData, true);

        if (is_null($organizationId)) {
            $organizationId = auth()->user()->organization_id;
        }

        return Upload::create([
            'user_id' => auth()->id(),
            'organization_id' => $organizationId,
            'file_name' => $fileProperties['fileName'],
            'file_path' => $fileProperties['fullPath'],
            'parsed_data' => $parsedData,
            'documents' => $parsedDataArray['totalDocuments'],
            'status' => Upload::STATUS_PENDING
        ]);
    }
}
