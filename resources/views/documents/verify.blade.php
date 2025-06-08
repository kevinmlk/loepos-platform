<x-layout>
    <x-header>
        AI Queue
        <x-slot:subText>
            Verifieer en koppel het document aan een client en dossier
            @if(isset($progress))
                <span class="ml-4 text-sm font-normal bg-blue text-white px-3 py-1 rounded-full">
                    {{ $progress['current'] }} van {{ $progress['total'] }}
                </span>
            @endif
        </x-slot:subText>
    </x-header>

    @if(!isset($documentData) || empty($documentData))
        <div class="flex items-center justify-center h-64">
            <div class="text-center">
                <i class="fas fa-info-circle text-6xl text-gray mb-4"></i>
                <p class="text-lg text-gray">Geen documenten om te verifiëren.</p>
                <a href="{{ route('documents.queue') }}" class="mt-4 inline-block text-blue hover:underline">
                    Terug naar de wachtrij
                </a>
            </div>
        </div>
    @else
    <div class="flex gap-6 h-[calc(100vh-10rem)]">
        {{-- Left Panel - Form --}}
        <div class="w-1/2 bg-white border border-light-gray rounded-lg p-6 overflow-y-auto">
            <form id="verifyForm" method="POST" action="{{ route('queue.verify.store') }}">
                @csrf
                
                <input type="hidden" name="original_document_id" value="{{ $documentData['original_document_id'] ?? '' }}">
                <input type="hidden" name="file_path" value="{{ $documentData['file_path'] ?? '' }}">
                <input type="hidden" name="file_name" value="{{ $documentData['file_name'] ?? '' }}">
                
                @php
                        // Extract parsed data for easier access
                        $parsedData = $documentData['parsed_data'] ?? [];
                        
                        // Helper function to extract value from nested structure
                        $getValue = function($keys, $default = '') use ($parsedData) {
                            // Convert single key to array
                            if (!is_array($keys)) {
                                $keys = [$keys];
                            }
                            
                            // Try each key in order using Laravel's data_get helper
                            foreach ($keys as $key) {
                                $value = data_get($parsedData, $key);
                                if ($value !== null && !is_array($value)) {
                                    return $value;
                                }
                            }
                            
                            return $default;
                        };
                        
                        // Extract sender and receiver info from parsed data
                        $senderName = $getValue([
                            'data.sender.name',
                            'sender.name', 
                            'sender', 
                            'from', 
                            'afzender', 
                            'creditor',
                            'content.documents.0.sender.name',
                            'content.documents.0.sender',
                            'documents.0.sender.name',
                            'documents.0.sender'
                        ], '');
                        
                        $receiverName = $getValue([
                            'data.receiver.name',
                            'receiver.name', 
                            'receiver', 
                            'to', 
                            'recipient', 
                            'ontvanger', 
                            'debtor', 
                            'clientName',
                            'content.documents.0.receiver.name',
                            'content.documents.0.receiver',
                            'documents.0.receiver.name',
                            'documents.0.receiver'
                        ], '');
                        
                        // Extract dates from parsed data
                        $documentDate = $getValue([
                            'data.documentDetails.dateCreated',
                            'data.documentDetails.dateSent',
                            'documentDate', 
                            'date', 
                            'invoiceDate', 
                            'dateCreated', 
                            'datum',
                            'content.documents.0.documentDetails.dateCreated',
                            'documents.0.documentDetails.dateCreated',
                            'send_date'
                        ], '');
                        
                        $dueDate = $getValue([
                            'data.documentDetails.dueDate',
                            'dueDate', 
                            'paymentDue', 
                            'vervaldatum', 
                            'expiryDate',
                            'content.documents.0.documentDetails.dueDate',
                            'documents.0.documentDetails.dueDate',
                            'due_date'
                        ], '');
                        
                        // Convert date format from various formats to yyyy-mm-dd for HTML date inputs
                        $formatDate = function($dateStr) {
                            if (empty($dateStr)) return '';
                            
                            // Dutch month names
                            $dutchMonths = [
                                'januari' => '01', 'februari' => '02', 'maart' => '03', 'april' => '04',
                                'mei' => '05', 'juni' => '06', 'juli' => '07', 'augustus' => '08',
                                'september' => '09', 'oktober' => '10', 'november' => '11', 'december' => '12'
                            ];
                            
                            // Check if date is in Dutch format (e.g., "22 juni 2025")
                            if (preg_match('/^(\d{1,2})\s+(\w+)\s+(\d{4})$/', $dateStr, $matches)) {
                                $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
                                $monthName = strtolower($matches[2]);
                                $year = $matches[3];
                                
                                if (isset($dutchMonths[$monthName])) {
                                    return "{$year}-{$dutchMonths[$monthName]}-{$day}";
                                }
                            }
                            
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
                        $detectedType = $getValue([
                            'data.documentDetails.documentType',
                            'documentType', 
                            'type', 
                            'document_type',
                            'content.documents.0.documentDetails.documentType',
                            'documents.0.documentDetails.documentType'
                        ], '');
                        
                        // Extract additional client info for pre-filling
                        $clientFirstName = $getValue(['data.receiver.firstName', 'receiver.firstName', 'clientFirstName', 'firstName'], '');
                        $clientLastName = $getValue(['data.receiver.lastName', 'receiver.lastName', 'clientLastName', 'lastName'], '');
                        $clientEmail = $getValue(['data.receiver.email', 'receiver.email', 'clientEmail', 'email'], '');
                        $clientPhone = $getValue(['data.receiver.phone', 'receiver.phone', 'clientPhone', 'phone'], '');
                        $clientFullAddress = $getValue(['data.receiver.address', 'receiver.address', 'clientAddress', 'address'], '');
                        $clientPostalCode = $getValue(['data.receiver.postalCode', 'receiver.postalCode', 'clientPostalCode', 'postalCode'], '');
                        $clientCity = $getValue(['data.receiver.city', 'receiver.city', 'clientCity', 'city'], '');
                        $clientNationalRegistryNumber = $getValue(['data.receiver.nationalRegistryNumber', 'receiver.nationalRegistryNumber', 'clientNationalRegistryNumber', 'nationalRegistryNumber'], '');
                        
                        // If we have a full address but no separate city/postal code, try to parse them
                        $clientAddress = $clientFullAddress;
                        if ($clientFullAddress && !$clientCity && !$clientPostalCode) {
                            // Try to match Belgian/Dutch address format: "Street 123, 1234 City" or "Street 123 City, ST 12345"
                            if (preg_match('/^(.+?),?\s+(\d{4,5})\s+(.+)$/', $clientFullAddress, $matches)) {
                                $clientAddress = trim($matches[1]);
                                $clientPostalCode = $matches[2];
                                $clientCity = trim($matches[3]);
                            } elseif (preg_match('/^(.+?)\s+(.+),\s*([A-Z]{2})\s+(\d{5}(?:-\d{4})?)$/', $clientFullAddress, $matches)) {
                                // US format: "Street City, ST 12345"
                                $clientAddress = trim($matches[1]);
                                $clientCity = trim($matches[2]);
                                $clientPostalCode = $matches[4];
                            }
                        }
                        
                        // Extract amount value
                        $amountValue = $getValue([
                            'data.documentDetails.invoiceAmount',
                            'documentDetails.invoiceAmount',
                            'amount', 
                            'total', 
                            'totalAmount', 
                            'invoiceAmount', 
                            'bedrag', 
                            'totaal',
                            'content.documents.0.documentDetails.invoiceAmount',
                            'documents.0.documentDetails.invoiceAmount'
                        ], '');
                    @endphp
                
                <div class="space-y-6">
                    {{-- Document Info - More compact --}}
                    <div class="bg-blue-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-semibold text-blue-900">{{ $documentData['file_name'] ?? 'Onbekend document' }}</h3>
                                <p class="text-sm text-blue-700 mt-1">Van: <span class="font-medium">{{ $senderName ?: 'Onbekend' }}</span></p>
                                <p class="text-sm text-blue-700">Aan: <span class="font-medium">{{ $receiverName ?: 'Onbekend' }}</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-blue-700">Type: <span class="font-semibold">{{ ucfirst($detectedType ?: 'Niet gedetecteerd') }}</span></p>
                                <p class="text-sm text-blue-700">Bedrag: <span class="font-semibold">€ {{ number_format((float)$amountValue, 2, ',', '.') ?: '-' }}</span></p>
                                <p class="text-sm text-blue-700">Pagina's: {{ implode(', ', $documentData['pages'] ?? []) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Step 1: Document Details --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 text-blue flex items-center">
                            <span class="bg-blue text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">1</span>
                            Document Gegevens
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Document Type --}}
                            <div>
                                <x-form.label for="type">Document Type*</x-form.label>
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
                            
                            {{-- Amount --}}
                            <div>
                                <x-form.label for="amount">Bedrag</x-form.label>
                                <x-form.input 
                                    type="number" 
                                    name="amount" 
                                    id="amount" 
                                    value="{{ $amountValue }}"
                                    step="0.01"
                                    placeholder="0.00"
                                />
                                <x-form.error name="amount" />
                            </div>
                        </div>
                        
                        {{-- Dates --}}
                        <div class="grid grid-cols-3 gap-4 mt-4">
                            <div>
                                <x-form.label for="send_date">Verzenddatum</x-form.label>
                                <x-form.input 
                                    type="date" 
                                    name="send_date" 
                                    id="send_date" 
                                    value="{{ $documentDate }}"
                                />
                                <x-form.error name="send_date" />
                            </div>
                            
                            <div>
                                <x-form.label for="receive_date">Ontvangstdatum*</x-form.label>
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
                                    value="{{ $dueDate }}"
                                />
                                <x-form.error name="due_date" />
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Parties --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 text-blue flex items-center">
                            <span class="bg-blue text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">2</span>
                            Afzender & Ontvanger
                        </h4>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-form.label for="sender">Afzender*</x-form.label>
                                <x-form.input 
                                    type="text" 
                                    name="sender" 
                                    id="sender" 
                                    value="{{ $senderName }}"
                                    required 
                                />
                                <x-form.error name="sender" />
                            </div>
                            
                            <div>
                                <x-form.label for="receiver">Ontvanger*</x-form.label>
                                <x-form.input 
                                    type="text" 
                                    name="receiver" 
                                    id="receiver" 
                                    value="{{ $receiverName }}"
                                    required 
                                />
                                <x-form.error name="receiver" />
                            </div>
                        </div>
                    </div>

                    {{-- Step 3: Client/Dossier Assignment --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 text-blue flex items-center">
                            <span class="bg-blue text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">3</span>
                            Koppel aan Client & Dossier
                        </h4>
                        
                        <div class="space-y-2">
                            <select 
                                name="dossier_id" 
                                id="dossier_id" 
                                class="w-full px-3 py-2 border border-light-gray rounded-lg focus:outline-none focus:border-blue"
                            >
                                <option value="">Selecteer een client/dossier</option>
                                @foreach($dossiersWithClients as $option)
                                    @php
                                        $isSelected = false;
                                        if (isset($receiverName) && $option['client_name']) {
                                            // Normalize both names for comparison
                                            $receiverNameLower = strtolower(trim($receiverName));
                                            $clientNameLower = strtolower(trim($option['client_name']));
                                            
                                            // Direct match
                                            if ($receiverNameLower === $clientNameLower) {
                                                $isSelected = true;
                                            } else {
                                                // Try to match "FirstName LastName" with "LastName FirstName"
                                                $receiverParts = explode(' ', $receiverNameLower);
                                                $clientParts = explode(' ', $clientNameLower);
                                                
                                                if (count($receiverParts) >= 2 && count($clientParts) >= 2) {
                                                    // Check if it's the same name but in different order
                                                    $receiverReversed = implode(' ', array_reverse($receiverParts));
                                                    if ($receiverReversed === $clientNameLower || $receiverNameLower === implode(' ', array_reverse($clientParts))) {
                                                        $isSelected = true;
                                                    }
                                                }
                                                
                                                // Also check if one contains the other (partial match)
                                                if (!$isSelected && (stripos($clientNameLower, $receiverNameLower) !== false || stripos($receiverNameLower, $clientNameLower) !== false)) {
                                                    $isSelected = true;
                                                }
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $option['id'] }}" {{ $isSelected ? 'selected' : '' }}>
                                        {{ $option['display'] }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" id="createNewBtn" class="text-sm text-blue hover:underline">
                                <i class="fas fa-plus mr-1"></i>Nieuwe client & dossier aanmaken
                            </button>
                        </div>
                        <x-form.error name="dossier_id" />
                        
                        {{-- New Client Form (hidden by default) --}}
                        <div id="newClientForm" class="hidden mt-4 p-4 bg-light-gray rounded-lg space-y-3">
                            <input type="hidden" name="create_new" id="create_new" value="0">
                            <h4 class="font-semibold mb-2 text-blue">Nieuwe Client & Dossier Aanmaken</h4>
                            <p class="text-sm text-gray mb-3">Vul de gegevens in om een nieuwe client en dossier aan te maken. De gemarkeerde velden zijn verplicht.</p>
                            
                            {{-- Name fields - Pre-filled from receiver data --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-form.label for="new_client_first_name">Voornaam*</x-form.label>
                                    <x-form.input 
                                        type="text" 
                                        name="new_client[first_name]" 
                                        id="new_client_first_name"
                                        value="{{ $clientFirstName ?: explode(' ', $receiverName)[0] ?? '' }}"
                                    />
                                </div>
                                <div>
                                    <x-form.label for="new_client_last_name">Achternaam*</x-form.label>
                                    <x-form.input 
                                        type="text" 
                                        name="new_client[last_name]" 
                                        id="new_client_last_name"
                                        value="{{ $clientLastName ?: (count(explode(' ', $receiverName)) > 1 ? implode(' ', array_slice(explode(' ', $receiverName), 1)) : '') }}"
                                    />
                                </div>
                            </div>
                            
                            {{-- Contact fields --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-form.label for="new_client_email">Email*</x-form.label>
                                    <x-form.input 
                                        type="email" 
                                        name="new_client[email]" 
                                        id="new_client_email"
                                        value="{{ $clientEmail }}"
                                        placeholder="client@voorbeeld.be"
                                    />
                                </div>
                                <div>
                                    <x-form.label for="new_client_phone">Telefoon</x-form.label>
                                    <x-form.input 
                                        type="text" 
                                        name="new_client[phone]" 
                                        id="new_client_phone"
                                        value="{{ $clientPhone }}"
                                        placeholder="+32 123 45 67 89"
                                    />
                                </div>
                            </div>
                            
                            {{-- Address - Pre-filled from receiver data --}}
                            <div>
                                <x-form.label for="new_client_address">Adres*</x-form.label>
                                <x-form.input 
                                    type="text" 
                                    name="new_client[address]" 
                                    id="new_client_address"
                                    value="{{ $clientAddress }}"
                                    placeholder="Straatnaam 123"
                                />
                            </div>
                            
                            {{-- Location fields --}}
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <x-form.label for="new_client_postal_code">Postcode*</x-form.label>
                                    <x-form.input 
                                        type="text" 
                                        name="new_client[postal_code]" 
                                        id="new_client_postal_code"
                                        value="{{ $clientPostalCode }}"
                                        placeholder="1000"
                                    />
                                </div>
                                <div>
                                    <x-form.label for="new_client_city">Stad*</x-form.label>
                                    <x-form.input 
                                        type="text" 
                                        name="new_client[city]" 
                                        id="new_client_city"
                                        value="{{ $clientCity }}"
                                        placeholder="Brussel"
                                    />
                                </div>
                                <div>
                                    <x-form.label for="new_client_national_registry_number">Rijksregisternummer</x-form.label>
                                    <x-form.input 
                                        type="text" 
                                        name="new_client[national_registry_number]" 
                                        id="new_client_national_registry_number"
                                        value="{{ $clientNationalRegistryNumber }}"
                                        placeholder="00.00.00-000.00"
                                    />
                                </div>
                            </div>
                            
                            <div class="flex gap-2 pt-2">
                                <button type="button" id="cancelNewClientBtn" class="flex-1 text-sm text-gray hover:text-red-600 py-2 px-4 border border-gray rounded hover:bg-red-50 transition-colors">
                                    <i class="fas fa-times mr-1"></i>Annuleren
                                </button>
                                <button type="button" id="confirmNewClientBtn" class="flex-1 text-sm text-white bg-green-600 hover:bg-green-700 py-2 px-4 rounded transition-colors">
                                    <i class="fas fa-check mr-1"></i>Bevestigen
                                </button>
                            </div>
                        </div>
                    </div>


                    {{-- Additional Fields if Available --}}
                    @php
                        $invoiceNumber = $getValue([
                            'data.documentDetails.caseNumber',
                            'invoiceNumber', 'number', 'factuurNummer', 'nummer',
                            'content.documents.0.documentDetails.caseNumber',
                            'documents.0.documentDetails.caseNumber'
                        ], '');
                        
                        $description = $getValue([
                            'data.documentDetails.summary',
                            'description', 'summary', 'omschrijving', 'beschrijving',
                            'content.documents.0.documentDetails.summary',
                            'documents.0.documentDetails.summary'
                        ], '');
                    @endphp
                    
                    @if($invoiceNumber || $description)
                        <div class="border border-light-gray rounded-lg p-4">
                            <h4 class="text-md font-semibold mb-3 text-gray">Additionele Informatie</h4>
                            <div class="grid grid-cols-2 gap-4">
                                @if($invoiceNumber)
                                    <div>
                                        <x-form.label for="verified_data_invoiceNumber">Referentie/Factuurnummer</x-form.label>
                                        <x-form.input 
                                            type="text" 
                                            name="verified_data[invoiceNumber]" 
                                            id="verified_data_invoiceNumber" 
                                            value="{{ $invoiceNumber }}"
                                        />
                                    </div>
                                @endif
                                
                                @if($description)
                                    <div class="{{ $invoiceNumber ? '' : 'col-span-2' }}">
                                        <x-form.label for="verified_data_description">Omschrijving</x-form.label>
                                        <x-form.input 
                                            type="text" 
                                            name="verified_data[description]" 
                                            id="verified_data_description" 
                                            value="{{ $description }}"
                                        />
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Action Buttons --}}
                    <div class="pt-4 space-y-2">
                        <button type="submit" class="w-full flex items-center justify-center rounded-xl px-6 h-12 text-button font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:cursor-pointer text-white bg-blue hover:bg-dark-blue focus:ring-blue-500">
                            <i class="fas fa-check mr-2"></i>
                            Verifieer Document
                            @if(isset($progress) && $progress['current'] < $progress['total'])
                                ({{ $progress['current'] }}/{{ $progress['total'] }})
                            @endif
                        </button>
                        
                        <button type="button" onclick="rejectDocument()" class="w-full flex items-center justify-center rounded-xl px-6 h-12 text-button font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 hover:cursor-pointer text-red-600 bg-red-100 hover:bg-red-200 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>
                            Document Weggooien
                        </button>
                        
                        @if(isset($progress) && $progress['total'] > 1)
                            <p class="text-sm text-center text-gray">
                                Na verificatie wordt automatisch het volgende document getoond
                            </p>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Panel - PDF Viewer --}}
        <div class="w-1/2 bg-light-gray rounded-lg overflow-hidden">
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
        // New client form toggle
        document.getElementById('createNewBtn').addEventListener('click', function() {
            document.getElementById('newClientForm').classList.remove('hidden');
            document.getElementById('dossier_id').disabled = true;
            document.getElementById('create_new').value = '1';
            
            // Add required attributes to new client fields
            document.getElementById('new_client_first_name').setAttribute('required', 'required');
            document.getElementById('new_client_last_name').setAttribute('required', 'required');
            document.getElementById('new_client_email').setAttribute('required', 'required');
            document.getElementById('new_client_address').setAttribute('required', 'required');
            document.getElementById('new_client_postal_code').setAttribute('required', 'required');
            document.getElementById('new_client_city').setAttribute('required', 'required');
        });
        
        document.getElementById('cancelNewClientBtn').addEventListener('click', function() {
            document.getElementById('newClientForm').classList.add('hidden');
            document.getElementById('dossier_id').disabled = false;
            document.getElementById('create_new').value = '0';
            
            // Remove required attributes from new client fields
            document.getElementById('new_client_first_name').removeAttribute('required');
            document.getElementById('new_client_last_name').removeAttribute('required');
            document.getElementById('new_client_email').removeAttribute('required');
            document.getElementById('new_client_address').removeAttribute('required');
            document.getElementById('new_client_postal_code').removeAttribute('required');
            document.getElementById('new_client_city').removeAttribute('required');
        });
        
        // Confirm new client button
        document.getElementById('confirmNewClientBtn').addEventListener('click', function() {
            // Validate required fields
            const requiredFields = ['new_client_first_name', 'new_client_last_name', 'new_client_email', 'new_client_address', 'new_client_postal_code', 'new_client_city'];
            let isValid = true;
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            if (!isValid) {
                alert('Vul alle verplichte velden in.');
                return;
            }
            
            // Close the form section to show it's confirmed
            document.getElementById('newClientForm').classList.add('opacity-75');
            document.getElementById('confirmNewClientBtn').disabled = true;
            document.getElementById('confirmNewClientBtn').innerHTML = '<i class="fas fa-check mr-1"></i>Bevestigd';
        });
        
        // Auto-select dossier based on receiver name from parsed data
        document.addEventListener('DOMContentLoaded', function() {
            const dossierSelect = document.getElementById('dossier_id');
            if (dossierSelect.selectedIndex === 0) { // No selection made yet
                // Check if any option was marked as selected based on name matching
                const selectedOption = dossierSelect.querySelector('option[selected]');
                if (selectedOption) {
                    dossierSelect.value = selectedOption.value;
                }
            }
        });
        
        // Form validation
        document.getElementById('verifyForm').addEventListener('submit', function(e) {
            const createNew = document.getElementById('create_new').value === '1';
            const dossierSelected = document.getElementById('dossier_id').value;
            
            if (!createNew && !dossierSelected) {
                e.preventDefault();
                alert('Selecteer een client/dossier of maak een nieuwe aan.');
                return false;
            }
            
            if (createNew) {
                const firstName = document.getElementById('new_client_first_name').value.trim();
                const lastName = document.getElementById('new_client_last_name').value.trim();
                const email = document.getElementById('new_client_email').value.trim();
                
                if (!firstName || !lastName || !email) {
                    e.preventDefault();
                    alert('Vul alle verplichte velden in voor de nieuwe client (voornaam, achternaam, email).');
                    return false;
                }
            }
        });
        
        // Reject document function
        function rejectDocument() {
            if (!confirm('Weet u zeker dat u dit document wilt weggooien?')) {
                return;
            }
            
            const documentId = document.querySelector('input[name="original_document_id"]').value;
            
            fetch(`/documents/${documentId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect based on whether there are more documents
                    window.location.href = data.redirect || '/documents/verify';
                } else {
                    alert(data.message || 'Er is een fout opgetreden bij het weggooien van het document.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er is een fout opgetreden bij het weggooien van het document.');
            });
        }
    </script>
    @endpush
    @endif
</x-layout>