<?php

// app/Services/DossierService.php

namespace App\Services;

use App\Models\Client;

class DossierService {

    public function determineDossierId(array $parsedDataArray): ?int {
        // Initialize variables for receiver details
        $receiverName = null;
        $receiverAddress = null;

        // Extract the details from parsed data
        if (isset($parsedDataArray['content']['documents'][0]['receiver'])) {
            $receiver = $parsedDataArray['content']['documents'][0]['receiver'];
            $receiverName = $receiver['name'] ?? null;
            $receiverAddress = $receiver['address'] ?? null;
        }

        // Try to find a matching client
        $client = null;
        if ($receiverName) {
            $nameParts = explode(' ', $receiverName, 2); // Split into first and last name
            $client = Client::where('first_name', 'like', '%' . ($nameParts[0] ?? '') . '%')
                ->orWhere('last_name', 'like', '%' . ($nameParts[1] ?? '') . '%')
                ->first();
        } elseif ($receiverAddress) {
            $client = Client::where('address', 'like', '%' . $receiverAddress . '%')->first();
        }

        // Determine the dossier_id
        if ($client) {
            $dossier = $client->dossier()->first(); // Get the first dossier
            return $dossier ? $dossier->id : null;
        }

        // Return null if no client or dossier is found
        return null;
    }
}
