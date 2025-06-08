<x-layout>
    {{-- Custom header without buttons --}}
    <header class="flex justify-between">
        <div>
            <h1 class="text-4xl font-bold">Documenten</h1>
            <p class="mt-1 text-dark-gray">
                Verifieer en koppel het document aan een client en dossier
                <span class="ml-4 text-sm font-normal bg-blue text-white px-3 py-1 rounded-full">
                    Stap 2 van 2
                </span>
            </p>
        </div>
    </header>

    {{-- Tab navigation --}}
    <div class="flex gap-4">
        <a
            href="/documents"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Overzicht
        </a>

        <a
            href="/upload"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Upload
        </a>

        <a
            href="/queue"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white relative"
        >
            Wachtrij
            @if($queueCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center">
                    {{ $queueCount }}
                </span>
            @endif
        </a>
    </div>

    <section class="border-2 border-light-gray rounded-lg flex flex-col h-[calc(100vh-16rem)]">
        {{-- Display any errors --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Er zijn fouten opgetreden:</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        @if(!isset($documentData) || empty($documentData))
            <div class="flex items-center justify-center h-full">
                <div class="text-center">
                    <i class="fas fa-info-circle text-6xl text-gray mb-4"></i>
                    <p class="text-lg text-gray">Geen documenten om te verifiëren.</p>
                    <a href="{{ route('documents.queue') }}" class="mt-4 inline-block text-blue hover:underline">
                        Terug naar de wachtrij
                    </a>
                </div>
            </div>
        @else
        <form id="verifyForm" method="POST" action="{{ route('queue.verify.store') }}" class="flex flex-col h-full">
        <div class="flex-1 flex gap-0 h-full">
        {{-- Left Panel - Form --}}
        <div class="w-1/2 bg-white border-r border-light-gray flex flex-col h-full">
            {{-- Document Info Header --}}
            <div class="p-4 px-6 border-b border-light-gray bg-light-gray flex-shrink-0">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-dark-gray mb-1">Document Verificatie</h3>
                        <p class="text-sm text-dark-gray">Controleer en vul de gegevens aan</p>
                    </div>
                    @if(isset($progress))
                        <span class="text-sm text-dark-gray">
                            Document {{ $progress['current'] }} van {{ $progress['total'] }}
                        </span>
                    @endif
                </div>
            </div>
            {{-- Form Content --}}
            <div class="flex-1 overflow-y-auto p-4 px-6" id="scrollableContent">
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
                
                <div class="space-y-6" id="formContent">
                    {{-- Step 1: Client/Dossier Assignment --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 text-blue flex items-center">
                            <span class="bg-blue text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">1</span>
                            Koppel aan Client & Dossier
                        </h4>
                        
                        <div class="space-y-4">
                            {{-- Search for existing client --}}
                            <div class="relative">
                                <x-form.label for="client_search">Zoek bestaande client</x-form.label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="client_search" 
                                        placeholder="Typ naam of emailadres..."
                                        class="w-full px-3 py-2 pl-10 border border-light-gray rounded-lg focus:outline-none focus:border-blue"
                                        autocomplete="off"
                                    >
                                    <i class="fas fa-search absolute left-3 top-3 text-gray"></i>
                                </div>
                                <div id="search_results" class="hidden absolute z-10 w-full mt-1 bg-white border border-light-gray rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                            </div>
                            
                            {{-- Selected client display --}}
                            <div id="selected_client" class="hidden bg-blue-50 p-3 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium text-blue-900" id="selected_client_name"></p>
                                        <p class="text-sm text-blue-700" id="selected_client_info"></p>
                                    </div>
                                    <button type="button" id="clear_selection" class="text-blue-600 hover:text-blue-800 hover:bg-blue-100 px-2 py-1 rounded transition-colors">
                                        <i class="fas fa-times mr-1"></i>Wissen
                                    </button>
                                </div>
                            </div>
                            
                            <input type="hidden" name="dossier_id" id="dossier_id" value="">
                            
                            {{-- Or create new --}}
                            <div class="relative">
                                <div class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-light-gray"></div>
                                </div>
                                <div class="relative flex justify-center text-sm">
                                    <span class="px-4 bg-white text-gray">of</span>
                                </div>
                            </div>
                            
                            <button type="button" id="createNewBtn" class="w-full text-blue border-2 border-blue rounded-lg py-2 hover:bg-blue hover:text-white transition-colors">
                                <i class="fas fa-plus mr-2"></i>Nieuwe client & dossier aanmaken
                            </button>
                        </div>
                        
                        {{-- New Client Form (hidden by default) --}}
                        <div id="newClientForm" class="hidden mt-4 space-y-4">
                            <input type="hidden" name="create_new" id="create_new" value="0">
                            
                            {{-- Only include new client fields when creating new --}}
                            <div id="newClientFields">
                            
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <h5 class="font-semibold text-blue-900 mb-2">Nieuwe Client Gegevens</h5>
                                
                                {{-- Name fields --}}
                                <div class="grid grid-cols-2 gap-3 mb-3">
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
                                <div class="grid grid-cols-2 gap-3 mb-3">
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
                                
                                {{-- Address --}}
                                <div class="mb-3">
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
                                <div class="grid grid-cols-3 gap-3 mb-3">
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
                                    <button type="button" id="cancelNewClientBtn" class="flex-1 text-dark-gray border border-gray rounded-lg py-2 hover:bg-gray hover:text-red-600 transition-colors">
                                        <i class="fas fa-times mr-1"></i>Annuleren
                                    </button>
                                    <button type="button" id="confirmNewClientBtn" class="flex-1 bg-blue text-white rounded-lg py-2 hover:bg-dark-blue transition-colors">
                                        <i class="fas fa-check mr-1"></i>Bevestigen
                                    </button>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>

                    {{-- Step 2: Document Details --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 text-blue flex items-center">
                            <span class="bg-blue text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">2</span>
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

                    {{-- Step 3: Parties --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-lg font-semibold mb-4 text-blue flex items-center">
                            <span class="bg-blue text-white rounded-full w-7 h-7 flex items-center justify-center mr-2 text-sm">3</span>
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
                    
                    {{-- Additional Fields - Always visible --}}
                    <div class="border border-light-gray rounded-lg p-4">
                        <h4 class="text-md font-semibold mb-3 text-gray">Additionele Informatie</h4>
                        <div class="mb-4">
                            <x-form.label for="verified_data_invoiceNumber">Referentie/Factuurnummer</x-form.label>
                            <x-form.input 
                                type="text" 
                                name="verified_data[invoiceNumber]" 
                                id="verified_data_invoiceNumber" 
                                value="{{ $invoiceNumber }}"
                                placeholder="Bijv: INV-2024-001"
                            />
                        </div>
                        
                        <div>
                            <x-form.label for="verified_data_description">Omschrijving</x-form.label>
                            <textarea 
                                name="verified_data[description]" 
                                id="verified_data_description" 
                                rows="3"
                                class="w-full px-3 py-2 border border-light-gray rounded-lg focus:outline-none focus:border-blue resize-none"
                                placeholder="Korte omschrijving van het document"
                            >{{ $description }}</textarea>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Fixed Action Buttons --}}
            <div class="p-4 px-6 border-t border-light-gray bg-light-gray flex-shrink-0">
                <div class="flex items-center justify-between">
                    <button type="button" id="rejectBtn" onclick="rejectDocument()" class="text-dark-gray hover:text-red-600 text-sm transition-colors" disabled>
                        Document weigeren
                    </button>
                    
                    <button type="submit" id="submitBtn" class="bg-blue text-white py-2 px-6 rounded-lg hover:bg-dark-blue disabled:bg-gray disabled:cursor-not-allowed transition-colors font-medium" disabled>
                        <i class="fas fa-save mr-2"></i>
                        Opslaan
                    </button>
                </div>
                
                @if(isset($progress) && $progress['total'] > 1)
                    <p class="text-xs text-center text-gray mt-2">
                        Na verificatie wordt automatisch het volgende document getoond
                    </p>
                @endif
            </div>
        </div>

        {{-- Right Panel - PDF Viewer --}}
        <div class="w-1/2 bg-white flex flex-col h-full">
            {{-- PDF Content --}}
            <div class="flex-1 bg-gray-100">
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
        </div>
        </form>
        @endif
    </section>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @endpush

    @push('scripts')
    <script>
        // Check if user has scrolled to bottom
        const scrollableContent = document.getElementById('scrollableContent');
        const submitBtn = document.getElementById('submitBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        
        function checkScrollPosition() {
            if (!scrollableContent || !submitBtn || !rejectBtn) {
                console.error('Required elements not found for scroll check');
                return;
            }
            
            const scrollHeight = scrollableContent.scrollHeight;
            const scrollTop = scrollableContent.scrollTop;
            const clientHeight = scrollableContent.clientHeight;
            const isAtBottom = scrollHeight - scrollTop <= clientHeight + 10;
            
            console.log('Scroll check:', { scrollHeight, scrollTop, clientHeight, isAtBottom });
            
            if (isAtBottom || scrollHeight <= clientHeight) {
                submitBtn.disabled = false;
                rejectBtn.disabled = false;
            }
        }
        
        // Check on scroll
        scrollableContent.addEventListener('scroll', checkScrollPosition);
        
        // Check on page load and after a delay
        setTimeout(checkScrollPosition, 100);
        setTimeout(checkScrollPosition, 500);
        
        // Also check when window resizes
        window.addEventListener('resize', checkScrollPosition);
        
        // New client form toggle
        document.getElementById('createNewBtn').addEventListener('click', function() {
            // Clear any selected client first
            if (!selectedClientDiv.classList.contains('hidden')) {
                clearSelectionBtn.click();
            }
            
            // Also clear the dossier_id value to ensure no client is connected
            document.getElementById('dossier_id').value = '';
            
            document.getElementById('newClientForm').classList.remove('hidden');
            document.getElementById('dossier_id').disabled = true;
            document.getElementById('create_new').value = '1';
            
            // Enable and add required attributes to new client fields
            const newClientFields = ['new_client_first_name', 'new_client_last_name', 'new_client_email', 
                                   'new_client_address', 'new_client_postal_code', 'new_client_city'];
            newClientFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = false;
                    if (fieldId !== 'new_client_phone' && fieldId !== 'new_client_national_registry_number') {
                        field.setAttribute('required', 'required');
                    }
                }
            });
        });
        
        document.getElementById('cancelNewClientBtn').addEventListener('click', function() {
            document.getElementById('newClientForm').classList.add('hidden');
            document.getElementById('dossier_id').disabled = false;
            document.getElementById('create_new').value = '0';
            
            // Disable and remove required attributes from new client fields
            const newClientFields = ['new_client_first_name', 'new_client_last_name', 'new_client_email', 
                                   'new_client_address', 'new_client_postal_code', 'new_client_city', 
                                   'new_client_phone', 'new_client_national_registry_number'];
            newClientFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = true;
                    field.removeAttribute('required');
                }
            });
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
        
        // Moved below after selectClient is defined
        
        // Form validation - ensure form exists before adding listener
        document.addEventListener('DOMContentLoaded', function() {
            const verifyForm = document.getElementById('verifyForm');
            if (!verifyForm) {
                console.error('Verify form not found!');
                return;
            }
            
            console.log('Adding submit listener to verify form');
            
            verifyForm.addEventListener('submit', function(e) {
                console.log('Form submission started');
                
                const createNew = document.getElementById('create_new').value === '1';
                const dossierSelected = document.getElementById('dossier_id').value;
                
                console.log('Form data:', {
                    createNew: createNew,
                    dossierSelected: dossierSelected,
                    dossier_id: document.getElementById('dossier_id').value,
                    type: document.getElementById('type').value,
                    sender: document.getElementById('sender').value,
                    receiver: document.getElementById('receiver').value,
                    receive_date: document.getElementById('receive_date').value
                });
                
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
                
                // Check other required fields
                const requiredFields = ['type', 'sender', 'receiver', 'receive_date'];
                for (const fieldId of requiredFields) {
                    const field = document.getElementById(fieldId);
                    if (!field || !field.value.trim()) {
                        e.preventDefault();
                        const fieldNames = {
                            'type': 'Document Type',
                            'sender': 'Afzender',
                            'receiver': 'Ontvanger',
                            'receive_date': 'Ontvangstdatum'
                        };
                        alert(`Vul het verplichte veld in: ${fieldNames[fieldId] || fieldId}`);
                        return false;
                    }
                }
                
                console.log('Form validation passed, submitting...');
            });
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
        
        // Client search functionality
        const clientSearchInput = document.getElementById('client_search');
        const searchResults = document.getElementById('search_results');
        const selectedClientDiv = document.getElementById('selected_client');
        const selectedClientName = document.getElementById('selected_client_name');
        const selectedClientInfo = document.getElementById('selected_client_info');
        const clearSelectionBtn = document.getElementById('clear_selection');
        const dossierIdInput = document.getElementById('dossier_id');
        
        let searchTimeout;
        
        // Handle client search
        clientSearchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            const searchTerm = e.target.value.trim();
            
            if (searchTerm.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }
            
            searchTimeout = setTimeout(() => {
                // Simulate search results - in production this would be an API call
                // For now, let's filter the existing dossier options
                const existingOptions = @json($dossiersWithClients);
                const filtered = existingOptions.filter(option => {
                    const searchLower = searchTerm.toLowerCase();
                    return option.display.toLowerCase().includes(searchLower);
                });
                
                if (filtered.length > 0) {
                    let resultsHtml = '';
                    filtered.forEach(option => {
                        resultsHtml += `
                            <div class="px-4 py-2 hover:bg-blue hover:text-white cursor-pointer search-result-item" 
                                 data-id="${option.id}" 
                                 data-name="${option.client_name || ''}"
                                 data-display="${option.display}">
                                ${option.display}
                            </div>
                        `;
                    });
                    searchResults.innerHTML = resultsHtml;
                    searchResults.classList.remove('hidden');
                    
                    // Add click handlers to results
                    searchResults.querySelectorAll('.search-result-item').forEach(item => {
                        item.addEventListener('click', function() {
                            selectClient(this.dataset.id, this.dataset.name, this.dataset.display);
                        });
                    });
                } else {
                    searchResults.innerHTML = '<div class="px-4 py-2 text-gray">Geen resultaten gevonden</div>';
                    searchResults.classList.remove('hidden');
                }
            }, 300);
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#client_search') && !e.target.closest('#search_results')) {
                searchResults.classList.add('hidden');
            }
        });
        
        // Handle client selection
        function selectClient(id, name, display) {
            // Make sure all elements exist
            if (!dossierIdInput || !clientSearchInput || !selectedClientDiv) {
                console.error('Required elements not found for selectClient');
                return;
            }
            
            // Close new client form if it's open
            const newClientForm = document.getElementById('newClientForm');
            if (newClientForm && !newClientForm.classList.contains('hidden')) {
                document.getElementById('cancelNewClientBtn').click();
            }
            
            dossierIdInput.value = id;
            clientSearchInput.value = '';
            clientSearchInput.disabled = true;
            searchResults.classList.add('hidden');
            
            selectedClientName.textContent = name || display.split(' - ')[0];
            selectedClientInfo.textContent = display;
            selectedClientDiv.classList.remove('hidden');
            
            // Keep create new button enabled so user can switch
            // const createNewBtn = document.getElementById('createNewBtn');
            // if (createNewBtn) {
            //     createNewBtn.disabled = true;
            // }
        }
        
        // Handle clear selection
        clearSelectionBtn.addEventListener('click', function() {
            dossierIdInput.value = '';
            clientSearchInput.disabled = false;
            clientSearchInput.value = '';
            selectedClientDiv.classList.add('hidden');
            // Button is always enabled now
            // document.getElementById('createNewBtn').disabled = false;
        });
        
        // Initially disable new client fields on page load
        document.addEventListener('DOMContentLoaded', function() {
            const newClientFields = ['new_client_first_name', 'new_client_last_name', 'new_client_email', 
                                   'new_client_address', 'new_client_postal_code', 'new_client_city', 
                                   'new_client_phone', 'new_client_national_registry_number'];
            newClientFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.disabled = true;
                }
            });
        });
        
        // Wait for DOM to be ready before auto-selecting
        window.addEventListener('load', function() {
            // Check if we have a receiver name to match
            const receiverName = @json($receiverName ?? '');
            console.log('Receiver name for auto-selection:', receiverName);
            if (receiverName) {
                const existingOptions = @json($dossiersWithClients);
                
                // Try to find a matching client
                let matchedOption = null;
                const receiverNameLower = receiverName.toLowerCase().trim();
                
                for (const option of existingOptions) {
                    if (option.client_name) {
                        const clientNameLower = option.client_name.toLowerCase().trim();
                        
                        // Direct match
                        if (receiverNameLower === clientNameLower) {
                            matchedOption = option;
                            break;
                        }
                        
                        // Try to match "FirstName LastName" with "LastName FirstName"
                        const receiverParts = receiverNameLower.split(' ');
                        const clientParts = clientNameLower.split(' ');
                        
                        if (receiverParts.length >= 2 && clientParts.length >= 2) {
                            const receiverReversed = receiverParts.slice().reverse().join(' ');
                            const clientReversed = clientParts.slice().reverse().join(' ');
                            if (receiverReversed === clientNameLower || receiverNameLower === clientReversed) {
                                matchedOption = option;
                                break;
                            }
                        }
                        
                        // Partial match
                        if (clientNameLower.includes(receiverNameLower) || receiverNameLower.includes(clientNameLower)) {
                            matchedOption = option;
                            // Don't break here, continue looking for exact matches
                        }
                    }
                }
                
                // If we found a match, auto-select it
                if (matchedOption) {
                    console.log('Auto-selecting client:', matchedOption);
                    selectClient(matchedOption.id, matchedOption.client_name, matchedOption.display);
                }
            }
        });
    </script>
    @endpush
</x-layout>