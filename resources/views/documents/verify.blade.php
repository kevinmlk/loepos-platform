<x-layout>
    <x-header>
        Document Verificatie
        <x-slot:subText>
            Verifieer en koppel het document aan een client en dossier
        </x-slot:subText>
    </x-header>

    <div class="flex gap-6 -mx-14 -mb-6 h-[calc(100vh-6rem)]">
        {{-- Left Panel - Form --}}
        <div class="w-1/2 bg-white p-6 overflow-y-auto">
            <form id="verifyForm" method="POST" action="{{ route('documents.verify.store') }}">
                @csrf
                
                <input type="hidden" name="original_document_id" value="{{ $documentData['original_document_id'] ?? '' }}">
                <input type="hidden" name="file_path" value="{{ $documentData['file_path'] ?? '' }}">
                <input type="hidden" name="file_name" value="{{ $documentData['file_name'] ?? '' }}">
                
                <div class="space-y-6">
                    {{-- Document Info --}}
                    <div class="border-b border-light-gray pb-4">
                        <h3 class="text-lg font-semibold mb-2">Document Informatie</h3>
                        <p class="text-sm text-gray">{{ $documentData['file_name'] ?? 'Onbekend document' }}</p>
                        <p class="text-sm text-gray">Pagina's: {{ implode(', ', $documentData['pages'] ?? []) }}</p>
                    </div>

                    {{-- Debug Section - Remove in production --}}
                    @if(config('app.debug'))
                        <div class="border border-red-300 bg-red-50 p-4 rounded">
                            <h4 class="font-semibold mb-2">Debug Info:</h4>
                            <pre class="text-xs overflow-auto">{{ json_encode($documentData, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif

                    {{-- Client Selection --}}
                    <div>
                        <x-form.label for="client_id">Geadresseerde (Client)</x-form.label>
                        <select 
                            name="client_id" 
                            id="client_id" 
                            class="w-full px-3 py-2 border border-light-gray rounded-lg focus:outline-none focus:border-blue"
                            required
                        >
                            <option value="">Selecteer een client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">
                                    {{ $client->last_name }}, {{ $client->first_name }} - {{ $client->email }}
                                </option>
                            @endforeach
                        </select>
                        <x-form.error name="client_id" />
                    </div>

                    {{-- Dossier Selection --}}
                    <div>
                        <x-form.label for="dossier_id">Dossier</x-form.label>
                        <select 
                            name="dossier_id" 
                            id="dossier_id" 
                            class="w-full px-3 py-2 border border-light-gray rounded-lg focus:outline-none focus:border-blue"
                            required
                        >
                            <option value="">Selecteer een dossier</option>
                            @foreach($dossiers as $dossier)
                                <option value="{{ $dossier->id }}" data-clients="{{ $dossier->clients->pluck('id')->join(',') }}">
                                    Dossier #{{ $dossier->id }} - 
                                    {{ $dossier->clients->map(function($c) { return $c->last_name . ', ' . $c->first_name; })->join(' & ') }}
                                </option>
                            @endforeach
                        </select>
                        <x-form.error name="dossier_id" />
                    </div>

                    @php
                        // Extract parsed data for easier access
                        $parsedData = $documentData['parsed_data'] ?? [];
                        $parsedContent = $parsedData['content'] ?? $parsedData;
                        
                        // Get the specific document data based on pages
                        $documentInfo = null;
                        if (isset($parsedContent['documents']) && is_array($parsedContent['documents'])) {
                            $requestedPages = $documentData['pages'] ?? [];
                            
                            // Find the document that matches our page range
                            foreach ($parsedContent['documents'] as $doc) {
                                if (isset($doc['startPage']) && isset($doc['endPage'])) {
                                    // Check if our requested pages fall within this document's range
                                    $startPage = $doc['startPage'];
                                    $endPage = $doc['endPage'];
                                    
                                    // If the first requested page falls within this document's range, use it
                                    if (!empty($requestedPages) && $requestedPages[0] >= $startPage && $requestedPages[0] <= $endPage) {
                                        $documentInfo = $doc;
                                        break;
                                    }
                                }
                            }
                            
                            // Fallback to first document if no match found
                            if (!$documentInfo && !empty($parsedContent['documents'])) {
                                $documentInfo = $parsedContent['documents'][0];
                            }
                        }
                        
                        // Helper function to extract value from nested structure
                        $getValue = function($keys, $default = '') use ($documentInfo, $parsedContent, $parsedData) {
                            // Convert single key to array
                            if (!is_array($keys)) {
                                $keys = [$keys];
                            }
                            
                            // Try each key in order
                            foreach ($keys as $key) {
                                // Check in document info first (most specific)
                                if ($documentInfo) {
                                    // Check direct properties
                                    if (isset($documentInfo[$key]) && !is_array($documentInfo[$key])) {
                                        return $documentInfo[$key];
                                    }
                                    
                                    // Check in sender
                                    if (isset($documentInfo['sender'][$key])) {
                                        return $documentInfo['sender'][$key];
                                    }
                                    
                                    // Check in receiver
                                    if (isset($documentInfo['receiver'][$key])) {
                                        return $documentInfo['receiver'][$key];
                                    }
                                    
                                    // Check in documentDetails
                                    if (isset($documentInfo['documentDetails'][$key])) {
                                        return $documentInfo['documentDetails'][$key];
                                    }
                                }
                                
                                // Then check in content
                                if (isset($parsedContent[$key])) {
                                    $value = $parsedContent[$key];
                                    if (!is_array($value)) {
                                        return $value;
                                    }
                                }
                                
                                // Finally check in root parsed data
                                if (isset($parsedData[$key])) {
                                    $value = $parsedData[$key];
                                    if (!is_array($value)) {
                                        return $value;
                                    }
                                }
                            }
                            
                            return $default;
                        };
                        
                        // Extract sender and receiver info
                        $senderName = $documentInfo['sender']['name'] ?? '';
                        $receiverName = $documentInfo['receiver']['name'] ?? '';
                        
                        // Extract dates from document info
                        $documentDate = $documentInfo['documentDetails']['dateCreated'] ?? $documentInfo['documentDetails']['date'] ?? '';
                        $dueDate = $documentInfo['documentDetails']['dueDate'] ?? '';
                        
                        // Convert date format from dd/mm/yyyy to yyyy-mm-dd for HTML date inputs
                        $formatDate = function($dateStr) {
                            if (empty($dateStr)) return '';
                            
                            // Check if date is in dd/mm/yyyy format
                            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $dateStr, $matches)) {
                                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                                $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
                                $year = $matches[3];
                                return "{$year}-{$month}-{$day}";
                            }
                            
                            // If already in yyyy-mm-dd format or other format, return as is
                            return $dateStr;
                        };
                        
                        $documentDate = $formatDate($documentDate);
                        $dueDate = $formatDate($dueDate);
                        
                        // Try to determine document type from parsed data
                        $detectedType = $documentInfo['documentDetails']['documentType'] ?? $getValue(['documentType', 'type', 'document_type'], '');
                    @endphp

                    {{-- Document Type --}}
                    <div>
                        <x-form.label for="type">Document Type</x-form.label>
                        <select 
                            name="type" 
                            id="type" 
                            class="w-full px-3 py-2 border border-light-gray rounded-lg focus:outline-none focus:border-blue"
                            required
                        >
                            <option value="">Selecteer type</option>
                            @foreach($documentTypes as $type)
                                <option value="{{ $type }}" {{ strtolower($detectedType) == strtolower($type) ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                        <x-form.error name="type" />
                    </div>

                    {{-- Sender/Receiver --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-form.label for="sender">Afzender</x-form.label>
                            <x-form.input 
                                type="text" 
                                name="sender" 
                                id="sender" 
                                value="{{ $senderName ?: $getValue(['sender', 'from', 'afzender', 'creditor'], '') }}"
                                required 
                            />
                            <x-form.error name="sender" />
                        </div>
                        
                        <div>
                            <x-form.label for="receiver">Ontvanger</x-form.label>
                            <x-form.input 
                                type="text" 
                                name="receiver" 
                                id="receiver" 
                                value="{{ $receiverName ?: $getValue(['receiver', 'to', 'recipient', 'ontvanger', 'debtor', 'clientName'], '') }}"
                                required 
                            />
                            <x-form.error name="receiver" />
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <x-form.label for="send_date">Verzenddatum</x-form.label>
                            <x-form.input 
                                type="date" 
                                name="send_date" 
                                id="send_date" 
                                value="{{ $documentDate ?: $getValue(['date', 'sendDate', 'invoiceDate', 'documentDate', 'datum'], '') }}"
                            />
                            <x-form.error name="send_date" />
                        </div>
                        
                        <div>
                            <x-form.label for="receive_date">Ontvangstdatum</x-form.label>
                            <x-form.input 
                                type="date" 
                                name="receive_date" 
                                id="receive_date" 
                                value="{{ now()->format('Y-m-d') }}"
                                required 
                            />
                            <x-form.error name="receive_date" />
                        </div>
                        
                        <div>
                            <x-form.label for="due_date">Vervaldatum</x-form.label>
                            <x-form.input 
                                type="date" 
                                name="due_date" 
                                id="due_date" 
                                value="{{ $dueDate ?: $getValue(['dueDate', 'paymentDue', 'vervaldatum', 'expiryDate'], '') }}"
                            />
                            <x-form.error name="due_date" />
                        </div>
                    </div>

                    {{-- Dynamic Fields from Parsed Data --}}
                    @if(isset($documentInfo['documentDetails']) && is_array($documentInfo['documentDetails']))
                        @php
                            // Fields already shown above
                            $shownFields = ['documentType', 'dateCreated', 'dueDate', 'date', 'dateSent'];
                            
                            // Filter out already shown fields and prepare additional fields from documentDetails
                            $additionalFields = [];
                            foreach ($documentInfo['documentDetails'] as $key => $value) {
                                if (!in_array($key, $shownFields) && !is_null($value) && $value !== '') {
                                    // Extract actual value if it's in an array/object format
                                    $displayValue = $value;
                                    if (is_array($value)) {
                                        if (isset($value['value'])) {
                                            $displayValue = $value['value'];
                                        } elseif (isset($value[0])) {
                                            $displayValue = $value[0];
                                        } else {
                                            $displayValue = json_encode($value);
                                        }
                                    }
                                    
                                    $additionalFields[$key] = $displayValue;
                                }
                            }
                        @endphp
                        
                        @if(count($additionalFields) > 0)
                            <div class="border-t border-light-gray pt-4">
                                <h4 class="text-lg font-semibold mb-4">Additionele Informatie</h4>
                                <div class="space-y-4">
                                    @foreach($additionalFields as $key => $value)
                                        <div>
                                            @php
                                                $labelMap = [
                                                    'invoiceAmount' => 'Factuurbedrag',
                                                    'caseNumber' => 'Dossiernummer',
                                                    'summary' => 'Omschrijving'
                                                ];
                                                $label = $labelMap[$key] ?? ucfirst(str_replace(['_', '-'], ' ', $key));
                                            @endphp
                                            <x-form.label for="verified_data_{{ $key }}">
                                                {{ $label }}
                                            </x-form.label>
                                            <x-form.input 
                                                type="text" 
                                                name="verified_data[{{ $key }}]" 
                                                id="verified_data_{{ $key }}" 
                                                value="{{ $key === 'invoiceAmount' ? '€ ' . number_format($value, 2, ',', '.') : $value }}"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif

                    {{-- Submit Button --}}
                    <div class="pt-4">
                        <button type="submit" class="w-full flex items-center justify-center rounded-xl px-6 h-12 text-button font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:cursor-pointer text-white bg-blue hover:bg-dark-blue focus:ring-blue-500">
                            <i class="fas fa-check mr-2"></i>
                            Verifieer Document
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Panel - PDF Viewer --}}
        <div class="w-1/2 bg-light-gray">
            @if(isset($documentData['file_path']))
                <iframe 
                    src="{{ asset('storage/' . $documentData['file_path']) }}"
                    class="w-full h-full"
                    title="Document Preview"
                ></iframe>
            @else
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray">Geen document preview beschikbaar</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Filter dossiers based on selected client
        document.getElementById('client_id').addEventListener('change', function() {
            const selectedClientId = this.value;
            const dossierSelect = document.getElementById('dossier_id');
            const options = dossierSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') return; // Skip the placeholder option
                
                const clientIds = option.dataset.clients ? option.dataset.clients.split(',') : [];
                if (selectedClientId && !clientIds.includes(selectedClientId)) {
                    option.style.display = 'none';
                    option.disabled = true;
                } else {
                    option.style.display = '';
                    option.disabled = false;
                }
            });
            
            // Reset dossier selection if current selection is hidden
            if (dossierSelect.selectedOptions[0] && dossierSelect.selectedOptions[0].disabled) {
                dossierSelect.value = '';
            }
        });

        // Auto-select dossier if client has only one
        document.getElementById('client_id').addEventListener('change', function() {
            const selectedClientId = this.value;
            const dossierSelect = document.getElementById('dossier_id');
            const visibleOptions = Array.from(dossierSelect.querySelectorAll('option:not([disabled])')).filter(opt => opt.value !== '');
            
            if (visibleOptions.length === 1) {
                dossierSelect.value = visibleOptions[0].value;
            }
        });
    </script>
    @endpush
</x-layout>