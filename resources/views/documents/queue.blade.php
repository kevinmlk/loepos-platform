<x-layout>
    <x-header>
        AI Queue
        <x-slot:subText>
            Controleer of we alles in de wachtrij juist hebben gesplitst.
        </x-slot:subText>
    </x-header>

    {{-- Pass documents data to JavaScript --}}
    <script>
        window.documentsData = @json($documents);
        // Add flag to indicate if documents are from uploads (pre-split)
        window.documentsFromUploads = {{ $documents->filter(function($doc) { return $doc->upload_id !== null; })->count() > 0 ? 'true' : 'false' }};
    </script>

    <div class="flex-1 flex gap-0 -mx-14 -mb-6 h-[calc(100vh-6rem)]">
        {{-- Left Panel - PDF Viewer --}}
        <div class="w-1/2 bg-white border-r border-light-gray flex flex-col h-full">
            {{-- Document Info Header --}}
            <div class="p-6 px-14 border-b border-light-gray bg-light-gray flex-shrink-0 h-[88px]">
                <div id="currentDocumentInfo">
                    <h3 class="text-lg font-semibold text-dark-gray mb-2">
                        <span id="uploadCounter">{{ $documents->count() }}</span> {{ $documents->count() == 1 ? 'upload' : 'uploads' }} - <span id="documentCounter">0</span> <span id="documentCounterText">documenten</span>
                    </h3>
                    <p class="text-sm text-dark-gray font-medium" id="currentDocumentName">Selecteer een document om te bekijken</p>
                </div>
            </div>

            {{-- PDF Pages Viewer --}}
            <div class="flex-1 overflow-y-auto p-6 px-14 min-h-0" id="pdfViewer">
                <div class="flex justify-center items-center h-full">
                    <div class="text-center text-gray">
                        <i class="fas fa-spinner fa-spin text-6xl mb-4"></i>
                        <p>Uploads laden...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel - Editor --}}
        <div class="w-1/2 bg-white flex flex-col h-full relative">
            {{-- Top Section with fixed heights --}}
            <div class="flex flex-col h-full">
                {{-- Toolbar --}}
                <div class="p-6 px-14 border-b border-light-gray bg-light-gray h-[88px] flex items-center flex-shrink-0">
                    <div class="flex space-x-2">
                        <button id="undoBtn" class="p-2 bg-gray rounded hover:bg-dark-gray hover:text-white disabled:opacity-50 transition-colors" disabled title="Ongedaan maken">
                            <i class="fas fa-undo"></i>
                        </button>
                        <button id="redoBtn" class="p-2 bg-gray rounded hover:bg-dark-gray hover:text-white disabled:opacity-50 transition-colors" disabled title="Opnieuw">
                            <i class="fas fa-redo"></i>
                        </button>
                        <button id="deleteBtn" class="p-2 bg-red-200 rounded hover:bg-red-500 hover:text-white disabled:opacity-50 transition-colors" disabled title="Verwijder geselecteerde">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button id="rotateBtn" class="p-2 bg-blue-200 rounded hover:bg-blue hover:text-white disabled:opacity-50 transition-colors" disabled title="Roteren">
                            <i class="fas fa-redo-alt"></i>
                        </button>
                        <button id="cutBtn" class="p-2 bg-yellow-200 rounded hover:bg-yellow-500 hover:text-white disabled:opacity-50 transition-colors" disabled title="Knip naar nieuw document">
                            <i class="fas fa-cut"></i>
                        </button>
                        <button id="viewBtn" class="p-2 bg-purple-200 rounded hover:bg-purple-500 hover:text-white disabled:opacity-50 transition-colors" disabled title="Bekijk geselecteerde pagina's">
                            <i class="fas fa-expand"></i>
                        </button>
                    </div>
                </div>

                {{-- Document Rows --}}
                <div class="flex-1 overflow-y-auto p-6 px-14" style="height: calc(100% - 200px);" id="documentRows">
                    <div class="text-center text-gray mt-20">
                        <i class="fas fa-layer-group text-6xl mb-4"></i>
                        <p>Verwerk een upload om documenten te splitsen</p>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="px-6 px-14 pt-6 pb-8 border-t border-light-gray bg-light-gray h-[112px] flex-shrink-0">
                    <button id="saveButton" class="w-full bg-blue text-white py-3 px-4 rounded-xl hover:bg-dark-blue disabled:bg-gray disabled:cursor-not-allowed transition-colors font-medium" disabled>
                    <i class="fas fa-save mr-2"></i>
                    Opslaan en Verwerken
                </button>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .document-row[draggable="true"]:hover {
            cursor: grab;
        }
        
        .document-row[draggable="true"]:active {
            cursor: grabbing;
        }
        
        .thumbnail[draggable="true"]:hover {
            cursor: grab;
        }
        
        .thumbnail[draggable="true"]:active {
            cursor: grabbing;
        }
        
        /* Show pointer cursor to indicate clickable thumbnails */
        .thumbnail {
            cursor: pointer;
        }
        
        .document-row.dragging {
            opacity: 0.5;
            transform: scale(0.95);
        }
        
        .thumbnail.dragging {
            opacity: 0.5;
            transform: scale(0.9);
        }
        
        .drop-zone {
            transition: all 0.2s ease;
        }
        
        /* Only show drop zones when dragging */
        .dragging-active .drop-zone:hover {
            width: 64px !important;
            background-color: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .drop-zone-active {
            width: 64px !important;
            background-color: #3B82F6 !important;
            border-radius: 4px;
            box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
        }
        
        /* Smooth collapse animation */
        .upload-documents-container {
            transition: all 0.3s ease;
            overflow: hidden;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        class PDFSplitter {
            constructor() {
                if (typeof document === 'undefined') {
                    console.error('This application must run in a browser environment');
                    return;
                }
                
                this.uploads = window.documentsData || [];
                this.allDocuments = []; // All documents from all uploads
                this.uploadPdfs = {}; // Store PDF docs by upload ID
                this.uploadPages = {}; // Store pages by upload ID
                this.currentUploadId = null;
                this.selectedDocument = -1; // No document selected initially
                this.selectedPages = new Set();
                this.lastSelectedPage = null; // Track last selected page for shift-click
                this.history = [];
                this.historyIndex = -1;
                this.hasUnsavedChanges = false;
                
                this.initializeEventListeners();
                this.setupPDFJS();
                this.initializeDragAndDrop();
                this.initializeUnloadWarning();
                
                // Load all uploads if available
                if (this.uploads.length > 0) {
                    this.loadAllUploads();
                }
            }

            setupPDFJS() {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }

            initializeDragAndDrop() {
                this.draggedElement = null;
                this.draggedType = null; // 'document' or 'page'
                this.draggedData = null;
                this.dropZones = new Set();
            }

            initializeUnloadWarning() {
                // Add beforeunload event listener
                window.addEventListener('beforeunload', (e) => {
                    if (this.hasUnsavedChanges && this.allDocuments.length > 0) {
                        const message = 'U heeft onopgeslagen wijzigingen. Weet u zeker dat u deze pagina wilt verlaten?';
                        e.preventDefault();
                        e.returnValue = message;
                        return message;
                    }
                });

                // Override click behavior for navigation links and buttons
                document.addEventListener('click', (e) => {
                    // Check if clicked element is a link or button that would navigate away
                    const link = e.target.closest('a[href]');
                    const button = e.target.closest('button[onclick*="location"]');
                    
                    if ((link || button) && this.hasUnsavedChanges && this.allDocuments.length > 0) {
                        const href = link ? link.getAttribute('href') : null;
                        
                        // Skip if it's an anchor link on the same page
                        if (href && href.startsWith('#')) return;
                        
                        // Skip if it's the save button
                        if (e.target.closest('#saveButton')) return;
                        
                        e.preventDefault();
                        
                        if (confirm('U heeft onopgeslagen wijzigingen. Weet u zeker dat u deze pagina wilt verlaten? Uw wijzigingen gaan verloren.')) {
                            this.hasUnsavedChanges = false;
                            if (link) {
                                window.location.href = href;
                            } else if (button) {
                                button.click();
                            }
                        }
                    }
                });
            }

            initializeEventListeners() {
                // Toolbar buttons
                document.getElementById('undoBtn').addEventListener('click', () => this.undo());
                document.getElementById('redoBtn').addEventListener('click', () => this.redo());
                document.getElementById('deleteBtn').addEventListener('click', () => this.deleteSelected());
                document.getElementById('rotateBtn').addEventListener('click', () => this.rotateSelected());
                document.getElementById('cutBtn').addEventListener('click', () => this.cutToNewDocument());
                document.getElementById('viewBtn').addEventListener('click', () => this.viewSelectedPages());
                document.getElementById('saveButton').addEventListener('click', () => this.saveDocuments());
                
                // Add click listener to document rows container to deselect when clicking outside
                document.getElementById('documentRows').addEventListener('click', (e) => {
                    // Check if click was on empty space (not on a document row or thumbnail)
                    if (e.target.id === 'documentRows' || 
                        e.target.classList.contains('upload-documents-container') ||
                        (!e.target.closest('.document-row') && !e.target.closest('.thumbnail') && !e.target.closest('h4'))) {
                        this.clearPageSelection();
                    }
                });
                
                // Also add listener to the PDF viewer area
                document.getElementById('pdfViewer').addEventListener('click', (e) => {
                    // If clicking on the viewer background (not on a page)
                    if (e.target.id === 'pdfViewer' || e.target.closest('.flex.justify-center.items-center.h-full')) {
                        this.clearPageSelection();
                    }
                });
            }

            async loadAllUploads() {
                const viewer = document.getElementById('pdfViewer');
                viewer.innerHTML = `
                    <div class="flex justify-center items-center h-full">
                        <div class="text-center text-gray">
                            <i class="fas fa-spinner fa-spin text-6xl mb-4"></i>
                            <p>Alle uploads laden...</p>
                        </div>
                    </div>
                `;

                try {
                    // Load all uploads concurrently
                    const loadPromises = this.uploads.map(async (upload) => {
                        const pdfPath = `/uploads/${upload.upload.id}/view`;
                        
                        const loadingTask = pdfjsLib.getDocument({
                            url: pdfPath,
                            httpHeaders: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            withCredentials: true
                        });
                        
                        const pdfDoc = await loadingTask.promise;
                        this.uploadPdfs[upload.id] = pdfDoc;
                        
                        // Extract all pages as images for this upload
                        const pages = [];
                        for (let i = 1; i <= pdfDoc.numPages; i++) {
                            const pageImage = await this.renderPageToImage(i, pdfDoc);
                            pages.push({
                                pageNumber: i,
                                imageBlob: pageImage,
                                rotation: 0,
                                uploadId: upload.id
                            });
                        }
                        this.uploadPages[upload.id] = pages;
                        
                        // Process this upload to create documents
                        await this.processUpload(upload, pages);
                    });
                    
                    await Promise.all(loadPromises);
                    
                    // Display all documents
                    this.displayAllDocuments();
                    
                    // Select first document if available
                    if (this.allDocuments.length > 0) {
                        this.selectDocument(0);
                    }
                    
                    // Save initial state for undo/redo
                    this.saveState();
                    
                    // Don't mark as unsaved just for loading
                    this.hasUnsavedChanges = false;
                    
                    // Enable save button
                    document.getElementById('saveButton').disabled = false;
                    
                } catch (error) {
                    console.error('Error loading uploads:', error);
                    alert('Fout bij het laden van de uploads');
                }
            }

            showNoMoreDocuments() {
                const viewer = document.getElementById('pdfViewer');
                viewer.innerHTML = `
                    <div class="flex justify-center items-center h-full">
                        <div class="text-center text-gray">
                            <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                            <p class="text-lg font-semibold">Alle uploads zijn verwerkt!</p>
                        </div>
                    </div>
                `;

                const documentRows = document.getElementById('documentRows');
                documentRows.innerHTML = `
                    <div class="text-center text-gray mt-20">
                        <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                        <p>Geen uploads meer om te verwerken</p>
                    </div>
                `;
                
                // Update counter and subtitle
                const subtitleElement = document.getElementById('currentDocumentName');
                subtitleElement.textContent = 'Alle uploads zijn verwerkt';
            }


            async renderPageToImage(pageNumber, pdfDoc = null) {
                const doc = pdfDoc || this.pdfDoc;
                const page = await doc.getPage(pageNumber);
                const viewport = page.getViewport({ scale: 2.0 });
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                await page.render({
                    canvasContext: context,
                    viewport: viewport
                }).promise;

                return new Promise((resolve) => {
                    canvas.toBlob(resolve, 'image/png');
                });
            }

            displayPDFPages() {
                const viewer = document.getElementById('pdfViewer');
                viewer.innerHTML = '';

                this.pages.forEach((page, index) => {
                    this.createPageElement(page, viewer);
                });
            }

            async processUpload(upload, pages) {
                const parsedData = upload.parsed_data;
                
                try {
                    let jsonData = null;
                    if (parsedData) {
                        jsonData = typeof parsedData === 'string' ? JSON.parse(parsedData) : parsedData;
                    }
                    
                    // Check if this is an upload with documentBoundaries
                    if (jsonData && jsonData.documentBoundaries) {
                        // Process based on documentBoundaries
                        jsonData.documentBoundaries.forEach((boundary, index) => {
                            const startPage = boundary.startPage;
                            const documentNumber = boundary.documentNumber;
                            
                            // Calculate end page
                            const endPage = (index < jsonData.documentBoundaries.length - 1) 
                                ? jsonData.documentBoundaries[index + 1].startPage - 1
                                : jsonData.totalPages;
                            
                            const documentPages = [];
                            for (let i = startPage; i <= endPage && i <= pages.length; i++) {
                                if (pages[i - 1]) {
                                    documentPages.push({
                                        ...pages[i - 1],
                                        originalIndex: i - 1
                                    });
                                }
                            }
                            
                            this.allDocuments.push({
                                id: `${upload.id}_${index}`,
                                uploadId: upload.id,
                                name: `${upload.file_name} - Document ${documentNumber}`,
                                pages: documentPages,
                                metadata: {
                                    startPage: startPage,
                                    endPage: endPage,
                                    documentNumber: documentNumber,
                                    totalDocuments: jsonData.totalDocuments
                                }
                            });
                        });
                    } else {
                        // If no boundaries, treat as single document
                        this.allDocuments.push({
                            id: `${upload.id}_0`,
                            uploadId: upload.id,
                            name: upload.file_name,
                            pages: pages,
                            metadata: { type: 'complete' }
                        });
                    }
                } catch (error) {
                    console.error('Error processing upload:', error);
                    // If error, create single document
                    this.allDocuments.push({
                        id: `${upload.id}_0`,
                        uploadId: upload.id,
                        name: upload.file_name,
                        pages: pages,
                        metadata: { type: 'complete' }
                    });
                }
            }

            async processPDF(docData) {
                if (!this.pages || this.pages.length === 0) {
                    console.error('No pages loaded');
                    return;
                }
                
                const parsedData = docData.parsed_data;
                
                try {
                    let jsonData = null;
                    if (parsedData) {
                        jsonData = typeof parsedData === 'string' ? JSON.parse(parsedData) : parsedData;
                        console.log('Parsed JSON:', jsonData);
                    }
                    
                    // Check if this is an upload with documentBoundaries
                    if (docData.upload_id && jsonData && jsonData.documentBoundaries) {
                        // Process based on documentBoundaries
                        this.documents = [];
                        
                        jsonData.documentBoundaries.forEach((boundary, index) => {
                            const startPage = boundary.startPage;
                            const documentNumber = boundary.documentNumber;
                            
                            // Calculate end page
                            const endPage = (index < jsonData.documentBoundaries.length - 1) 
                                ? jsonData.documentBoundaries[index + 1].startPage - 1
                                : jsonData.totalPages;
                            
                            const documentPages = [];
                            for (let i = startPage; i <= endPage && i <= this.pages.length; i++) {
                                if (this.pages[i - 1]) {
                                    documentPages.push({
                                        ...this.pages[i - 1],
                                        originalIndex: i - 1
                                    });
                                }
                            }
                            
                            this.documents.push({
                                id: index,
                                name: `${docData.file_name} - Document ${documentNumber}`,
                                pages: documentPages,
                                metadata: {
                                    startPage: startPage,
                                    endPage: endPage,
                                    documentNumber: documentNumber,
                                    totalDocuments: jsonData.totalDocuments
                                }
                            });
                        });
                    } else if (!jsonData || !jsonData.content || !jsonData.content.documents || !Array.isArray(jsonData.content.documents)) {
                        // If no document structure, treat as single document
                        jsonData = {
                            content: {
                                documents: [{
                                    startPage: 1,
                                    endPage: this.pdfDoc.numPages,
                                    type: 'complete'
                                }]
                            }
                        };
                        this.documents = this.createDocumentsFromJSON(jsonData, docData.file_name);
                    } else {
                        this.documents = this.createDocumentsFromJSON(jsonData, docData.file_name);
                    }
                    
                    this.displayDocuments();
                    if (this.documents.length > 0) {
                        this.selectDocument(0);
                    }
                    this.saveState();
                    
                    // Enable save button
                    document.getElementById('saveButton').disabled = false;
                } catch (error) {
                    console.error('Error processing JSON:', error);
                    // If JSON parsing fails, create single document
                    this.documents = [{
                        id: 0,
                        name: `${docData.file_name} - Compleet`,
                        pages: [...this.pages],
                        metadata: { type: 'complete' }
                    }];
                    this.displayDocuments();
                    this.selectDocument(0);
                    this.saveState();
                    document.getElementById('saveButton').disabled = false;
                }
            }

            createDocumentsFromJSON(jsonData, documentName) {
                const documents = [];
                
                jsonData.content.documents.forEach((doc, index) => {
                    const startPage = doc.startPage || 1;
                    const endPage = doc.endPage || this.pages.length;
                    
                    const documentPages = [];
                    for (let i = startPage; i <= endPage && i <= this.pages.length; i++) {
                        if (this.pages[i - 1]) {
                            documentPages.push({
                                ...this.pages[i - 1],
                                originalIndex: i - 1
                            });
                        }
                    }
                    
                    documents.push({
                        id: index,
                        name: `${documentName} - Deel ${index + 1}`,
                        pages: documentPages,
                        metadata: doc
                    });
                });

                return documents;
            }

            displayAllDocuments() {
                const container = document.getElementById('documentRows');
                container.innerHTML = '';

                // Group documents by upload
                const documentsByUpload = {};
                this.allDocuments.forEach(doc => {
                    if (!documentsByUpload[doc.uploadId]) {
                        documentsByUpload[doc.uploadId] = [];
                    }
                    documentsByUpload[doc.uploadId].push(doc);
                });

                // Display documents grouped by upload
                this.uploads.forEach(upload => {
                    const uploadDocs = documentsByUpload[upload.id] || [];
                    
                    if (uploadDocs.length > 0) {
                        // Add upload header
                        const uploadHeader = document.createElement('div');
                        uploadHeader.className = 'mb-3 pt-4 pb-2 border-b border-gray';
                        
                        // Format the date
                        let dateString = '';
                        if (upload.created_at) {
                            const date = new Date(upload.created_at);
                            const day = String(date.getDate()).padStart(2, '0');
                            const month = String(date.getMonth() + 1).padStart(2, '0');
                            const year = date.getFullYear();
                            const hours = String(date.getHours()).padStart(2, '0');
                            const minutes = String(date.getMinutes()).padStart(2, '0');
                            dateString = `${day}/${month}/${year} ${hours}:${minutes}`;
                        }
                        
                        // Create document count text
                        const docCount = uploadDocs.length;
                        const docCountText = docCount === 1 ? '1 document' : `${docCount} documenten`;
                        
                        uploadHeader.innerHTML = `
                            <h4 class="text-md font-semibold text-dark-gray cursor-pointer hover:text-blue flex items-center justify-between" data-upload-id="${upload.id}">
                                <span>
                                    <i class="fas fa-chevron-up mr-2 transition-transform duration-200 collapse-arrow"></i>
                                    <i class="fas fa-file-pdf mr-2"></i>${upload.file_name} <span class="font-normal text-gray">- ${docCountText}</span>
                                </span>
                                <span class="text-sm font-normal text-gray">${dateString}</span>
                            </h4>
                        `;
                        
                        // Create a container for the documents that can be collapsed
                        const documentsContainer = document.createElement('div');
                        documentsContainer.className = 'upload-documents-container';
                        documentsContainer.dataset.uploadId = upload.id;
                        
                        uploadHeader.addEventListener('click', (e) => {
                            const arrow = uploadHeader.querySelector('.collapse-arrow');
                            const isCollapsed = documentsContainer.style.display === 'none';
                            
                            if (isCollapsed) {
                                documentsContainer.style.display = 'block';
                                arrow.classList.remove('fa-chevron-down');
                                arrow.classList.add('fa-chevron-up');
                            } else {
                                documentsContainer.style.display = 'none';
                                arrow.classList.remove('fa-chevron-up');
                                arrow.classList.add('fa-chevron-down');
                            }
                            
                            // If clicking on the file name area (not just the arrow), also display in viewer
                            if (!e.target.classList.contains('collapse-arrow')) {
                                this.selectedDocument = -1;
                                this.selectedPages.clear();
                                this.updateDocumentSelection();
                                this.displayUploadInViewer(upload.id);
                            }
                        });
                        
                        container.appendChild(uploadHeader);

                        // Add documents for this upload
                        uploadDocs.forEach((doc, index) => {
                            // Find the correct global index by comparing document IDs
                            const globalIndex = this.allDocuments.findIndex(d => d.id === doc.id);
                            const docRow = this.createDocumentRow(doc, globalIndex);
                            // Add data attribute for easy selection tracking
                            docRow.dataset.documentIndex = globalIndex;
                            documentsContainer.appendChild(docRow);
                        });
                        
                        // Add upload-specific drop zone at the end of documents container
                        this.addUploadDocumentDropZone(documentsContainer, upload.id);
                        
                        // Add the documents container after the header
                        container.appendChild(documentsContainer);
                    }
                });


                if (this.allDocuments.length > 0 && this.selectedDocument >= this.allDocuments.length) {
                    this.selectedDocument = 0;
                }
                
                // Update document counter
                this.updateDocumentCounter();
                
                this.updateToolbarState();
            }

            createDocumentRow(documentData, index) {
                const row = document.createElement('div');
                row.className = `document-row border rounded-lg mb-4 p-4 cursor-pointer transition-all ${
                    index === this.selectedDocument ? 'bg-transparant-blue border-blue' : 'bg-white border-gray hover:bg-light-gray'
                }`;
                row.dataset.documentIndex = index;
                
                // Make document row draggable
                row.draggable = true;
                row.dataset.dragType = 'document';
                row.dataset.documentId = documentData.id;

                const header = document.createElement('div');
                header.className = 'flex items-center justify-between mb-3';
                
                const title = document.createElement('h3');
                title.className = 'text-lg font-semibold text-black';
                title.textContent = documentData.name;
                
                const pageCount = document.createElement('span');
                pageCount.className = `text-sm ${index === this.selectedDocument ? 'text-white' : 'text-gray'}`;
                pageCount.textContent = `${documentData.pages.length} pagina${documentData.pages.length !== 1 ? "'s" : ''}`;
                
                header.appendChild(title);
                header.appendChild(pageCount);

                const thumbnailContainer = document.createElement('div');
                thumbnailContainer.className = 'flex flex-wrap gap-2 relative';

                documentData.pages.forEach((page, pageIndex) => {
                    // Add drop zone before each thumbnail
                    if (pageIndex === 0) {
                        const dropZone = this.createDropZone(index, pageIndex, 'before');
                        thumbnailContainer.appendChild(dropZone);
                    }
                    
                    const thumbnail = this.createThumbnail(page, index, pageIndex);
                    thumbnailContainer.appendChild(thumbnail);
                    
                    // Add drop zone after each thumbnail
                    const dropZoneAfter = this.createDropZone(index, pageIndex, 'after');
                    thumbnailContainer.appendChild(dropZoneAfter);
                });

                row.appendChild(header);
                row.appendChild(thumbnailContainer);

                row.addEventListener('click', (e) => {
                    if (!e.target.closest('.thumbnail')) {
                        this.selectDocument(index);
                    }
                });

                // Add drag and drop event listeners for document row
                row.addEventListener('dragstart', (e) => this.handleDocumentDragStart(e, documentData, index));
                row.addEventListener('dragover', (e) => this.handleDragOver(e));
                row.addEventListener('drop', (e) => this.handleDocumentDrop(e, documentData, index));

                return row;
            }

            createThumbnail(page, docIndex, pageIndex) {
                const thumbnail = document.createElement('div');
                thumbnail.className = 'thumbnail relative border border-gray rounded cursor-pointer hover:border-blue transition-all overflow-hidden';
                thumbnail.dataset.docIndex = docIndex;
                thumbnail.dataset.pageIndex = pageIndex;
                
                // Make thumbnail draggable
                thumbnail.draggable = true;
                thumbnail.dataset.dragType = 'page';
                thumbnail.dataset.pageNumber = page.pageNumber;

                const imgWrapper = document.createElement('div');
                imgWrapper.className = 'w-16 h-20 flex items-center justify-center bg-white';
                
                const img = document.createElement('img');
                img.src = URL.createObjectURL(page.imageBlob);
                
                // Handle rotation for thumbnails
                if (page.rotation % 180 !== 0) {
                    // For 90/270 degree rotations, swap dimensions
                    img.className = 'h-16 w-auto';
                } else {
                    // For 0/180 degree rotations
                    img.className = 'w-16 h-auto';
                }
                
                img.style.transform = `rotate(${page.rotation}deg)`;
                img.style.maxWidth = page.rotation % 180 !== 0 ? '80px' : '64px';
                img.style.maxHeight = page.rotation % 180 !== 0 ? '64px' : '80px';

                const pageNumber = document.createElement('div');
                pageNumber.className = 'absolute top-0 left-0 bg-black bg-opacity-75 text-white text-xs px-1 rounded-br z-10';
                pageNumber.textContent = page.pageNumber;

                imgWrapper.appendChild(img);
                thumbnail.appendChild(imgWrapper);
                thumbnail.appendChild(pageNumber);

                thumbnail.addEventListener('click', (e) => {
                    e.stopPropagation();
                    this.handlePageClick(docIndex, pageIndex, e);
                });

                // Add double-click to open in lightbox
                thumbnail.addEventListener('dblclick', (e) => {
                    e.stopPropagation();
                    e.preventDefault();
                    
                    // If no pages are selected, select only this page
                    if (this.selectedPages.size === 0) {
                        this.togglePageSelection(docIndex, pageIndex);
                    }
                    
                    // Open lightbox with selected pages
                    this.viewSelectedPages();
                });

                // Add drag event listeners for thumbnails (only drag start, no drop)
                thumbnail.addEventListener('dragstart', (e) => this.handlePageDragStart(e, page, docIndex, pageIndex));

                return thumbnail;
            }

            createDropZone(docIndex, pageIndex, position) {
                const dropZone = document.createElement('div');
                dropZone.className = 'drop-zone w-2 h-20 bg-transparent border-2 border-transparent transition-all duration-200 flex-shrink-0';
                dropZone.dataset.docIndex = docIndex;
                dropZone.dataset.pageIndex = pageIndex;
                dropZone.dataset.position = position;
                dropZone.dataset.dropType = 'insertion';
                
                // Add drag and drop event listeners
                dropZone.addEventListener('dragover', (e) => this.handleDropZoneDragOver(e));
                dropZone.addEventListener('drop', (e) => this.handleDropZoneDrop(e, docIndex, pageIndex, position));
                dropZone.addEventListener('dragenter', (e) => this.handleDropZoneEnter(e));
                dropZone.addEventListener('dragleave', (e) => this.handleDropZoneLeave(e));
                
                return dropZone;
            }

            selectDocument(index) {
                this.selectedDocument = index;
                this.selectedPages.clear();
                this.updateDocumentSelection();
                this.updateToolbarState();
                
                if (this.allDocuments[index]) {
                    const doc = this.allDocuments[index];
                    this.displaySelectedDocumentInViewer(doc);
                    
                    // Update subtitle with selected document name
                    const subtitleElement = document.getElementById('currentDocumentName');
                    subtitleElement.textContent = `Geselecteerd: ${doc.name}`;
                    
                    // Also display the upload this document belongs to
                    this.displayUploadInViewer(doc.uploadId);
                }
            }

            updateDocumentSelection() {
                document.querySelectorAll('.document-row').forEach((row) => {
                    const documentIndex = parseInt(row.dataset.documentIndex);
                    const pageCount = row.querySelector('.text-sm');
                    
                    if (documentIndex === this.selectedDocument) {
                        row.classList.remove('bg-white', 'border-gray', 'hover:bg-light-gray');
                        row.classList.add('bg-transparant-blue', 'border-blue');
                        if (pageCount) {
                            pageCount.classList.remove('text-gray');
                            pageCount.classList.add('text-white');
                        }
                    } else {
                        row.classList.remove('bg-transparant-blue', 'border-blue');
                        row.classList.add('bg-white', 'border-gray', 'hover:bg-light-gray');
                        if (pageCount) {
                            pageCount.classList.remove('text-white');
                            pageCount.classList.add('text-gray');
                        }
                    }
                });
            }

            displaySelectedDocumentInViewer(documentData) {
                const viewer = document.getElementById('pdfViewer');
                viewer.innerHTML = '';

                const totalPages = documentData.pages.length;
                documentData.pages.forEach((page) => {
                    this.createPageElement(page, viewer, totalPages);
                });
            }

            displayUploadInViewer(uploadId) {
                const upload = this.uploads.find(u => u.id === uploadId);
                if (!upload) return;

                // Update subtitle with upload name when no document is selected
                if (this.selectedDocument === -1) {
                    const subtitleElement = document.getElementById('currentDocumentName');
                    subtitleElement.textContent = `Geselecteerd: ${upload.file_name}`;
                }

                // Get pages for this upload
                const pages = this.uploadPages[uploadId] || [];
                
                const viewer = document.getElementById('pdfViewer');
                viewer.innerHTML = '';

                const totalPages = pages.length;
                pages.forEach((page) => {
                    this.createPageElement(page, viewer, totalPages);
                });

                this.currentUploadId = uploadId;
            }

            createPageElement(page, container, totalPages = null) {
                const pageContainer = document.createElement('div');
                pageContainer.className = 'mb-4 border border-gray rounded-lg overflow-hidden shadow-sm';
                
                const pageHeader = document.createElement('div');
                pageHeader.className = 'bg-light-gray px-3 py-2 text-sm font-medium text-dark-gray';
                
                // Show "Pagina n van n" format if totalPages is provided
                if (totalPages !== null) {
                    pageHeader.textContent = `Pagina ${page.pageNumber} van ${totalPages}`;
                } else {
                    pageHeader.textContent = `Pagina ${page.pageNumber}`;
                }
                
                const imgWrapper = document.createElement('div');
                imgWrapper.className = 'p-4 bg-white';
                
                const imgContainer = document.createElement('div');
                imgContainer.className = 'relative mx-auto flex justify-center items-center';
                
                const img = document.createElement('img');
                img.src = URL.createObjectURL(page.imageBlob);
                img.className = 'block max-w-full max-h-full';
                
                // Load image to get dimensions and calculate proper sizing
                img.onload = function() {
                    const containerWidth = imgWrapper.offsetWidth - 32; // subtract padding
                    const isRotated = page.rotation % 180 !== 0;
                    
                    if (isRotated) {
                        // For 90/270 degree rotations
                        // Calculate scale to fit rotated image within container width
                        const scale = Math.min(containerWidth / img.naturalHeight, 1);
                        const displayWidth = img.naturalWidth * scale;
                        const displayHeight = img.naturalHeight * scale;
                        
                        // Set container to match rotated dimensions
                        imgContainer.style.width = `${displayHeight}px`;
                        imgContainer.style.height = `${displayWidth}px`;
                        
                        // Set image size maintaining aspect ratio
                        img.style.width = `${displayWidth}px`;
                        img.style.height = `${displayHeight}px`;
                        img.style.position = 'absolute';
                        img.style.top = '50%';
                        img.style.left = '50%';
                        img.style.transform = `translate(-50%, -50%) rotate(${page.rotation}deg)`;
                        img.style.transformOrigin = 'center center';
                    } else {
                        // For 0/180 degree rotations
                        imgContainer.style.width = '100%';
                        imgContainer.style.height = 'auto';
                        
                        img.style.width = '100%';
                        img.style.height = 'auto';
                        img.style.position = 'static';
                        img.style.transform = `rotate(${page.rotation}deg)`;
                        img.style.transformOrigin = 'center center';
                    }
                };
                
                imgContainer.appendChild(img);
                imgWrapper.appendChild(imgContainer);
                pageContainer.appendChild(pageHeader);
                pageContainer.appendChild(imgWrapper);
                container.appendChild(pageContainer);
            }

            togglePageSelection(docIndex, pageIndex) {
                const key = `${docIndex}-${pageIndex}`;
                
                if (this.selectedPages.has(key)) {
                    // If page is already selected, deselect it
                    this.selectedPages.delete(key);
                } else {
                    // Check if we already have pages selected from a different document
                    if (this.selectedPages.size > 0) {
                        // Get the document index of the first selected page
                        const firstSelectedKey = Array.from(this.selectedPages)[0];
                        const firstSelectedDocIndex = parseInt(firstSelectedKey.split('-')[0]);
                        
                        // If trying to select from a different document, clear existing selection first
                        if (firstSelectedDocIndex !== docIndex) {
                            this.selectedPages.clear();
                        }
                    }
                    
                    // Add the new page selection
                    this.selectedPages.add(key);
                }
                
                this.updateThumbnailSelection();
                this.updateToolbarState();
            }

            updateThumbnailSelection() {
                document.querySelectorAll('.thumbnail').forEach(thumb => {
                    const docIndex = thumb.dataset.docIndex;
                    const pageIndex = thumb.dataset.pageIndex;
                    const key = `${docIndex}-${pageIndex}`;
                    
                    if (this.selectedPages.has(key)) {
                        thumb.classList.add('ring-2', 'ring-blue', 'bg-transparant-blue');
                    } else {
                        thumb.classList.remove('ring-2', 'ring-blue', 'bg-transparant-blue');
                    }
                });
            }

            clearPageSelection() {
                this.selectedPages.clear();
                this.lastSelectedPage = null;
                this.updateThumbnailSelection();
                this.updateToolbarState();
            }

            handlePageClick(docIndex, pageIndex, event) {
                const key = `${docIndex}-${pageIndex}`;
                
                if (event.shiftKey && this.lastSelectedPage) {
                    // Shift-click: select range
                    const lastDocIndex = this.lastSelectedPage.docIndex;
                    const lastPageIndex = this.lastSelectedPage.pageIndex;
                    
                    // Only allow range selection within the same document
                    if (lastDocIndex === docIndex) {
                        // Clear existing selection
                        this.selectedPages.clear();
                        
                        // Determine range
                        const startIndex = Math.min(lastPageIndex, pageIndex);
                        const endIndex = Math.max(lastPageIndex, pageIndex);
                        
                        // Select all pages in range
                        for (let i = startIndex; i <= endIndex; i++) {
                            const rangeKey = `${docIndex}-${i}`;
                            this.selectedPages.add(rangeKey);
                        }
                    } else {
                        // Different document, start new selection
                        this.selectedPages.clear();
                        this.selectedPages.add(key);
                        this.lastSelectedPage = { docIndex, pageIndex };
                    }
                } else if (event.ctrlKey || event.metaKey) {
                    // Ctrl/Cmd-click: toggle individual selection
                    this.togglePageSelection(docIndex, pageIndex);
                    if (this.selectedPages.has(key)) {
                        this.lastSelectedPage = { docIndex, pageIndex };
                    }
                } else {
                    // Regular click: select only this page
                    this.selectedPages.clear();
                    this.selectedPages.add(key);
                    this.lastSelectedPage = { docIndex, pageIndex };
                }
                
                this.updateThumbnailSelection();
                this.updateToolbarState();
            }

            updateDocumentCounter() {
                const documentCount = this.allDocuments.length;
                const counterElement = document.getElementById('documentCounter');
                const counterTextElement = document.getElementById('documentCounterText');
                
                if (counterElement) {
                    counterElement.textContent = documentCount;
                }
                
                if (counterTextElement) {
                    counterTextElement.textContent = documentCount === 1 ? 'document' : 'documenten';
                }
            }

            addUploadDocumentDropZone(container, uploadId) {
                const dropZone = document.createElement('div');
                dropZone.className = 'upload-document-drop-zone mt-4 p-4 border-2 border-dashed border-gray rounded-lg text-center text-gray transition-all duration-200';
                dropZone.dataset.uploadId = uploadId;
                dropZone.innerHTML = `
                    <div class="flex items-center justify-center gap-2">
                        <i class="fas fa-plus-circle text-lg opacity-50"></i>
                        <p class="text-sm">Sleep pagina's hierheen voor nieuw document</p>
                    </div>
                `;

                // Add drag and drop event listeners
                dropZone.addEventListener('dragover', (e) => this.handleUploadDocumentDragOver(e));
                dropZone.addEventListener('drop', (e) => this.handleUploadDocumentDrop(e, uploadId));
                dropZone.addEventListener('dragenter', (e) => this.handleUploadDocumentDragEnter(e));
                dropZone.addEventListener('dragleave', (e) => this.handleUploadDocumentDragLeave(e));

                container.appendChild(dropZone);
            }

            updateToolbarState() {
                const hasSelection = this.selectedPages.size > 0;
                const hasDocuments = this.allDocuments.length > 0;
                
                document.getElementById('deleteBtn').disabled = !hasSelection;
                document.getElementById('rotateBtn').disabled = !hasSelection;
                document.getElementById('cutBtn').disabled = !hasSelection;
                document.getElementById('viewBtn').disabled = !hasSelection;
                document.getElementById('undoBtn').disabled = this.historyIndex <= 0;
                document.getElementById('redoBtn').disabled = this.historyIndex >= this.history.length - 1;
                document.getElementById('saveButton').disabled = !hasDocuments;
            }

            deleteSelected() {
                if (this.selectedPages.size === 0) return;
                
                this.saveState();
                this.markAsModified();
                
                // Create a deep copy of all documents
                const newDocuments = this.allDocuments.map(doc => ({ 
                    ...doc, 
                    pages: [...doc.pages] 
                }));
                
                // Sort selected pages by pageIndex in descending order to avoid index shifting issues
                const selectedPagesArray = Array.from(this.selectedPages).map(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    return { docIndex, pageIndex };
                }).sort((a, b) => b.pageIndex - a.pageIndex);
                
                // Remove pages from documents
                selectedPagesArray.forEach(({ docIndex, pageIndex }) => {
                    if (newDocuments[docIndex] && newDocuments[docIndex].pages[pageIndex]) {
                        newDocuments[docIndex].pages.splice(pageIndex, 1);
                    }
                });

                // Remove empty documents
                this.allDocuments = newDocuments.filter(doc => doc.pages.length > 0);
                
                // Update selected document index if it's no longer valid
                if (this.selectedDocument >= this.allDocuments.length) {
                    this.selectedDocument = -1;
                }
                
                this.selectedPages.clear();
                this.displayAllDocuments();
                this.updateDocumentSelection();
                this.updateToolbarState();
            }

            rotateSelected() {
                if (this.selectedPages.size === 0) return;
                
                this.saveState();
                this.markAsModified();
                
                // Rotate selected pages
                this.selectedPages.forEach(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    if (this.allDocuments[docIndex] && this.allDocuments[docIndex].pages[pageIndex]) {
                        this.allDocuments[docIndex].pages[pageIndex].rotation += 90;
                        if (this.allDocuments[docIndex].pages[pageIndex].rotation >= 360) {
                            this.allDocuments[docIndex].pages[pageIndex].rotation = 0;
                        }
                        
                        // Also update the page in uploadPages for consistency
                        const uploadId = this.allDocuments[docIndex].uploadId;
                        const originalPageNum = this.allDocuments[docIndex].pages[pageIndex].pageNumber;
                        if (this.uploadPages[uploadId]) {
                            const uploadPage = this.uploadPages[uploadId].find(p => p.pageNumber === originalPageNum);
                            if (uploadPage) {
                                uploadPage.rotation = this.allDocuments[docIndex].pages[pageIndex].rotation;
                            }
                        }
                    }
                });

                // Clear selections to force redraw
                this.selectedPages.clear();
                this.displayAllDocuments();
                this.updateDocumentSelection();
                
                // Refresh the viewer if we have a current upload displayed
                if (this.currentUploadId) {
                    this.displayUploadInViewer(this.currentUploadId);
                }
                
                this.updateToolbarState();
            }

            cutToNewDocument() {
                if (this.selectedPages.size === 0) return;
                
                this.saveState();
                this.markAsModified();
                
                // Get selected pages with their document and page information
                const selectedPagesArray = Array.from(this.selectedPages).map(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    return {
                        docIndex,
                        pageIndex,
                        page: { ...this.allDocuments[docIndex].pages[pageIndex] }
                    };
                }).filter(item => item.page) // Filter out any invalid pages
                  .sort((a, b) => a.page.pageNumber - b.page.pageNumber);

                if (selectedPagesArray.length === 0) return;

                // Get the first selected page's upload ID for the new document
                const firstDoc = this.allDocuments[selectedPagesArray[0].docIndex];
                const uploadId = firstDoc.uploadId;
                
                // Get upload name and determine the new document number
                const upload = this.uploads.find(u => u.id === uploadId);
                const uploadName = upload ? upload.file_name : 'Document';
                const existingNewDocs = this.allDocuments.filter(doc => 
                    doc.uploadId === uploadId && 
                    doc.name.includes('Nieuw document')
                );
                const newDocNumber = existingNewDocs.length + 1;

                // Create new document with selected pages
                const newDocument = {
                    id: `${uploadId}_new_${Date.now()}`,
                    uploadId: uploadId,
                    name: `${uploadName} - Nieuw document ${newDocNumber}`,
                    pages: selectedPagesArray.map(item => ({ ...item.page })),
                    metadata: { type: 'custom' }
                };

                // Group pages to remove by document index and sort by page index descending
                const pagesToRemove = new Map();
                selectedPagesArray.forEach(item => {
                    if (!pagesToRemove.has(item.docIndex)) {
                        pagesToRemove.set(item.docIndex, []);
                    }
                    pagesToRemove.get(item.docIndex).push(item.pageIndex);
                });

                // Remove pages from original documents (in reverse order to avoid index issues)
                pagesToRemove.forEach((pageIndices, docIndex) => {
                    pageIndices.sort((a, b) => b - a); // Sort in descending order
                    pageIndices.forEach(pageIndex => {
                        if (this.allDocuments[docIndex] && this.allDocuments[docIndex].pages[pageIndex]) {
                            this.allDocuments[docIndex].pages.splice(pageIndex, 1);
                        }
                    });
                });

                // Remove empty documents and add new document
                this.allDocuments = this.allDocuments.filter(doc => doc.pages.length > 0);
                this.allDocuments.push(newDocument);
                
                // Update selected document index if it's no longer valid
                if (this.selectedDocument >= this.allDocuments.length - 1) {
                    this.selectedDocument = -1;
                }
                
                this.selectedPages.clear();
                this.displayAllDocuments();
                this.updateDocumentSelection();
                this.updateToolbarState();
            }

            viewSelectedPages() {
                if (this.selectedPages.size === 0) return;
                
                // Get selected pages with their document and page information
                const selectedPagesArray = Array.from(this.selectedPages).map(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    return {
                        docIndex,
                        pageIndex,
                        page: this.allDocuments[docIndex].pages[pageIndex]
                    };
                }).filter(item => item.page) // Filter out any invalid pages
                  .sort((a, b) => a.page.pageNumber - b.page.pageNumber);

                if (selectedPagesArray.length === 0) return;

                // Convert page blobs to URLs for lightbox
                const imageUrls = selectedPagesArray.map(item => {
                    return URL.createObjectURL(item.page.imageBlob);
                });

                // Create unique ID for this lightbox instance
                const lightboxId = 'lightbox_' + Date.now();

                // Create hidden links for fslightbox
                const container = document.createElement('div');
                container.style.display = 'none';
                container.id = lightboxId;
                
                imageUrls.forEach((url, index) => {
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('data-fslightbox', lightboxId);
                    link.setAttribute('data-type', 'image');
                    container.appendChild(link);
                });
                
                document.body.appendChild(container);

                // Open the lightbox
                if (typeof refreshFsLightbox !== 'undefined') {
                    refreshFsLightbox();
                }

                // Click the first link to open lightbox
                const firstLink = container.querySelector('a');
                if (firstLink) {
                    firstLink.click();
                }

                // Clean up after a delay
                setTimeout(() => {
                    // Clean up blob URLs
                    imageUrls.forEach(url => URL.revokeObjectURL(url));
                    // Remove container
                    if (container.parentNode) {
                        container.parentNode.removeChild(container);
                    }
                }, 60000); // Clean up after 1 minute
            }

            saveState() {
                const state = JSON.parse(JSON.stringify({
                    allDocuments: this.allDocuments,
                    selectedDocument: this.selectedDocument
                }));
                
                this.history = this.history.slice(0, this.historyIndex + 1);
                this.history.push(state);
                this.historyIndex++;
                
                // Limit history size
                if (this.history.length > 50) {
                    this.history.shift();
                    this.historyIndex--;
                }
                
                this.updateToolbarState();
            }

            markAsModified() {
                this.hasUnsavedChanges = true;
            }

            undo() {
                if (this.historyIndex > 0) {
                    this.historyIndex--;
                    const state = this.history[this.historyIndex];
                    this.allDocuments = JSON.parse(JSON.stringify(state.allDocuments || state.documents || []));
                    this.selectedDocument = state.selectedDocument;
                    this.selectedPages.clear();
                    this.displayAllDocuments();
                }
            }

            redo() {
                if (this.historyIndex < this.history.length - 1) {
                    this.historyIndex++;
                    const state = this.history[this.historyIndex];
                    this.allDocuments = JSON.parse(JSON.stringify(state.allDocuments || state.documents || []));
                    this.selectedDocument = state.selectedDocument;
                    this.selectedPages.clear();
                    this.displayAllDocuments();
                }
            }

            async convertPageImagesToBase64() {
                const base64Images = [];
                
                for (const page of this.pages) {
                    try {
                        const blob = page.imageBlob;
                        const reader = new FileReader();
                        const base64 = await new Promise((resolve, reject) => {
                            reader.onloadend = () => resolve(reader.result);
                            reader.onerror = reject;
                            reader.readAsDataURL(blob);
                        });
                        base64Images.push(base64);
                    } catch (error) {
                        console.error('Error converting image to base64:', error);
                        base64Images.push(null);
                    }
                }
                
                return base64Images;
            }
            
            async saveDocuments() {
                if (this.allDocuments.length === 0) return;
                
                const saveButton = document.getElementById('saveButton');
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verwerken...';
                
                try {
                    // Prepare all documents for processing
                    const allDocumentsToProcess = this.allDocuments.map(doc => ({
                        originalDocId: doc.uploadId,
                        name: doc.name,
                        pages: doc.pages.map(p => p.pageNumber),
                        pageImages: doc.pages.map(p => p.imageData || null),
                        metadata: doc.metadata
                    }));

                    // Send all documents to server in a single request
                    const response = await fetch('/documents/process-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            documents: allDocumentsToProcess
                        })
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        console.log('Server response:', result);
                        
                        if (result.redirect) {
                            // Show progress message
                            if (result.processed && result.total) {
                                saveButton.innerHTML = `<i class="fas fa-check mr-2"></i>Verwerkt ${result.processed}/${result.total} documenten. Doorverwijzen...`;
                            }
                            
                            // Redirect to verify page after a short delay
                            console.log('Redirecting to:', result.redirect);
                            setTimeout(() => {
                                window.location.href = result.redirect;
                            }, 500);
                            return;
                        } else {
                            console.warn('No redirect URL in response');
                        }
                    } else {
                        const errorText = await response.text();
                        console.error('Server error:', errorText);
                        throw new Error('Server error bij het verwerken van documenten');
                    }
                    
                    // If no redirect, all uploads processed successfully
                    alert('Alle uploads succesvol verwerkt!');
                    
                    // Mark as saved
                    this.hasUnsavedChanges = false;
                    
                    // Clear all data
                    this.allDocuments = [];
                    this.uploads = [];
                    this.selectedPages.clear();
                    this.history = [];
                    this.historyIndex = -1;
                    
                    // Update counters
                    const uploadCounterElement = document.getElementById('uploadCounter');
                    const documentCounterElement = document.getElementById('documentCounter');
                    if (uploadCounterElement) {
                        uploadCounterElement.textContent = '0';
                    }
                    if (documentCounterElement) {
                        documentCounterElement.textContent = '0';
                    }
                    
                    // Show completion message
                    const subtitleElement = document.getElementById('currentDocumentName');
                    subtitleElement.textContent = 'Alle uploads zijn verwerkt';
                    
                    const viewer = document.getElementById('pdfViewer');
                    viewer.innerHTML = `
                        <div class="flex justify-center items-center h-full">
                            <div class="text-center text-gray">
                                <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                                <p class="text-lg font-semibold">Alle uploads zijn verwerkt!</p>
                            </div>
                        </div>
                    `;
                    
                    const documentRows = document.getElementById('documentRows');
                    documentRows.innerHTML = `
                        <div class="text-center text-gray mt-20">
                            <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                            <p>Geen uploads meer om te verwerken</p>
                        </div>
                    `;
                } catch (error) {
                    console.error('Error saving documents:', error);
                    
                    alert('Er is een fout opgetreden bij het verwerken van de uploads: ' + error.message);
                    
                    // Re-enable save button
                    saveButton.disabled = false;
                    saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Opslaan en Verwerken';
                }
            }

            // Drag and Drop Handlers
            handleDocumentDragStart(e, documentData, index) {
                this.draggedElement = e.target;
                this.draggedType = 'document';
                this.draggedData = {
                    documentData,
                    index,
                    id: documentData.id
                };
                
                e.target.style.opacity = '0.5';
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', e.target.outerHTML);
            }

            handlePageDragStart(e, page, docIndex, pageIndex) {
                this.draggedElement = e.target;
                this.draggedType = 'page';
                
                const key = `${docIndex}-${pageIndex}`;
                
                // If this page is not selected, clear selection and select only this page
                if (!this.selectedPages.has(key)) {
                    this.selectedPages.clear();
                    this.selectedPages.add(key);
                    this.updateThumbnailSelection();
                }
                
                // Prepare data for all selected pages
                const selectedPagesArray = Array.from(this.selectedPages).map(selectedKey => {
                    const [selDocIndex, selPageIndex] = selectedKey.split('-').map(Number);
                    return {
                        key: selectedKey,
                        docIndex: selDocIndex,
                        pageIndex: selPageIndex,
                        page: { ...this.allDocuments[selDocIndex].pages[selPageIndex] }
                    };
                }).filter(item => item.page); // Filter out any invalid pages
                
                this.draggedData = {
                    isMultiPage: selectedPagesArray.length > 1,
                    pages: selectedPagesArray,
                    sourceDocIndex: docIndex,
                    sourcePageIndex: pageIndex
                };
                
                // Make all selected thumbnails semi-transparent
                document.querySelectorAll('.thumbnail').forEach(thumb => {
                    const thumbKey = `${thumb.dataset.docIndex}-${thumb.dataset.pageIndex}`;
                    if (this.selectedPages.has(thumbKey)) {
                        thumb.style.opacity = '0.5';
                    }
                });
                
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/html', e.target.outerHTML);
                e.stopPropagation(); // Prevent document drag
                
                // Add dragging-active class to disable hover effects
                document.getElementById('documentRows').classList.add('dragging-active');
            }

            handleDragOver(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            handleDocumentDrop(e, targetDocumentData, targetIndex) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!this.draggedData) return;
                
                if (this.draggedType === 'document') {
                    this.handleDocumentReorder(targetIndex);
                } else if (this.draggedType === 'page') {
                    this.handlePageToDocumentDrop(targetIndex);
                }
                
                this.cleanupDrag();
            }


            canDropOn(target) {
                if (!this.draggedData) return false;
                
                if (this.draggedType === 'document') {
                    return target.closest('.document-row') !== null;
                } else if (this.draggedType === 'page') {
                    return target.closest('.drop-zone') !== null || target.closest('.document-row') !== null;
                }
                
                return false;
            }

            handleDocumentReorder(targetIndex) {
                if (this.draggedData.index === targetIndex) return;
                
                this.saveState();
                this.markAsModified();
                
                const sourceIndex = this.draggedData.index;
                const documentToMove = this.allDocuments[sourceIndex];
                
                // Remove from source position
                this.allDocuments.splice(sourceIndex, 1);
                
                // Insert at target position
                let insertIndex = targetIndex;
                if (sourceIndex < targetIndex) {
                    insertIndex = targetIndex - 1;
                }
                
                this.allDocuments.splice(insertIndex, 0, documentToMove);
                
                // Update selected document index
                if (this.selectedDocument === sourceIndex) {
                    this.selectedDocument = insertIndex;
                } else if (this.selectedDocument > sourceIndex && this.selectedDocument <= targetIndex) {
                    this.selectedDocument--;
                } else if (this.selectedDocument < sourceIndex && this.selectedDocument >= targetIndex) {
                    this.selectedDocument++;
                }
                
                this.displayAllDocuments();
                this.updateDocumentSelection();
            }

            handlePageToDocumentDrop(targetDocIndex) {
                if (this.draggedData.sourceDocIndex === targetDocIndex) return;
                
                this.saveState();
                this.markAsModified();
                
                const sourceDocIndex = this.draggedData.sourceDocIndex;
                const sourcePageIndex = this.draggedData.sourcePageIndex;
                const pageToMove = { ...this.draggedData.page };
                
                // Remove page from source document
                this.allDocuments[sourceDocIndex].pages.splice(sourcePageIndex, 1);
                
                // Add page to target document
                this.allDocuments[targetDocIndex].pages.push(pageToMove);
                
                // Remove empty documents
                this.allDocuments = this.allDocuments.filter(doc => doc.pages.length > 0);
                
                // Update selected document if it was removed
                if (this.selectedDocument >= this.allDocuments.length) {
                    this.selectedDocument = -1;
                }
                
                this.displayAllDocuments();
                this.updateDocumentSelection();
            }

            handlePageReorder(targetDocIndex, targetPageIndex) {
                const sourceDocIndex = this.draggedData.sourceDocIndex;
                const sourcePageIndex = this.draggedData.sourcePageIndex;
                
                if (sourceDocIndex === targetDocIndex && sourcePageIndex === targetPageIndex) return;
                
                this.saveState();
                this.markAsModified();
                
                const pageToMove = { ...this.draggedData.page };
                
                if (sourceDocIndex === targetDocIndex) {
                    // Reordering within same document
                    this.allDocuments[sourceDocIndex].pages.splice(sourcePageIndex, 1);
                    
                    let insertIndex = targetPageIndex;
                    if (sourcePageIndex < targetPageIndex) {
                        insertIndex = targetPageIndex - 1;
                    }
                    
                    this.allDocuments[sourceDocIndex].pages.splice(insertIndex, 0, pageToMove);
                } else {
                    // Moving between documents
                    this.allDocuments[sourceDocIndex].pages.splice(sourcePageIndex, 1);
                    this.allDocuments[targetDocIndex].pages.splice(targetPageIndex, 0, pageToMove);
                    
                    // Remove empty documents
                    this.allDocuments = this.allDocuments.filter(doc => doc.pages.length > 0);
                    
                    // Update selected document if it was removed
                    if (this.selectedDocument >= this.allDocuments.length) {
                        this.selectedDocument = -1;
                    }
                }
                
                this.displayAllDocuments();
                this.updateDocumentSelection();
            }

            cleanupDrag() {
                // Reset all thumbnail opacities
                document.querySelectorAll('.thumbnail').forEach(thumb => {
                    thumb.style.opacity = '';
                });
                
                // Remove all drop zone active styling
                document.querySelectorAll('.drop-zone-active').forEach(el => {
                    el.classList.remove('drop-zone-active');
                });
                
                // Remove dragging-active class
                document.getElementById('documentRows').classList.remove('dragging-active');
                
                this.draggedElement = null;
                this.draggedType = null;
                this.draggedData = null;
            }

            // Drop Zone Specific Handlers
            handleDropZoneDragOver(e) {
                if (this.draggedType !== 'page') return;
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            handleDropZoneEnter(e) {
                if (this.draggedType !== 'page') return;
                e.preventDefault();
                e.target.classList.add('drop-zone-active');
            }

            handleDropZoneLeave(e) {
                e.target.classList.remove('drop-zone-active');
            }

            handleDropZoneDrop(e, docIndex, pageIndex, position) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!this.draggedData || this.draggedType !== 'page') return;
                
                // Remove drop zone styling
                e.target.classList.remove('drop-zone-active');
                
                // Calculate insertion index based on position
                let insertionIndex = pageIndex;
                if (position === 'after') {
                    insertionIndex = pageIndex + 1;
                }
                
                this.handlePageInsertionDrop(docIndex, insertionIndex);
                this.cleanupDrag();
            }

            handlePageInsertionDrop(targetDocIndex, insertionIndex) {
                if (!this.draggedData || !this.draggedData.pages) return;
                
                this.saveState();
                this.markAsModified();
                
                if (this.draggedData.isMultiPage) {
                    // Handle multiple pages
                    const pagesToMove = this.draggedData.pages.map(item => ({ ...item.page }));
                    
                    // Group pages to remove by document index and sort by page index descending
                    const pagesToRemove = new Map();
                    this.draggedData.pages.forEach(item => {
                        if (!pagesToRemove.has(item.docIndex)) {
                            pagesToRemove.set(item.docIndex, []);
                        }
                        pagesToRemove.get(item.docIndex).push(item.pageIndex);
                    });
                    
                    // Remove pages from source documents (in reverse order to avoid index issues)
                    pagesToRemove.forEach((pageIndices, docIndex) => {
                        pageIndices.sort((a, b) => b - a); // Sort in descending order
                        pageIndices.forEach(pageIndex => {
                            if (this.allDocuments[docIndex] && this.allDocuments[docIndex].pages[pageIndex]) {
                                this.allDocuments[docIndex].pages.splice(pageIndex, 1);
                            }
                        });
                    });
                    
                    // Insert all pages at the target location
                    this.allDocuments[targetDocIndex].pages.splice(insertionIndex, 0, ...pagesToMove);
                    
                } else {
                    // Handle single page (existing logic)
                    const sourceDocIndex = this.draggedData.sourceDocIndex;
                    const sourcePageIndex = this.draggedData.sourcePageIndex;
                    
                    // Don't allow dropping on the same position
                    if (sourceDocIndex === targetDocIndex && 
                        (sourcePageIndex === insertionIndex || sourcePageIndex === insertionIndex - 1)) {
                        return;
                    }
                    
                    const pageToMove = { ...this.draggedData.pages[0].page };
                    
                    if (sourceDocIndex === targetDocIndex) {
                        // Moving within same document
                        this.allDocuments[sourceDocIndex].pages.splice(sourcePageIndex, 1);
                        
                        // Adjust insertion index if we removed an element before it
                        let adjustedIndex = insertionIndex;
                        if (sourcePageIndex < insertionIndex) {
                            adjustedIndex = insertionIndex - 1;
                        }
                        
                        this.allDocuments[sourceDocIndex].pages.splice(adjustedIndex, 0, pageToMove);
                    } else {
                        // Moving between documents
                        this.allDocuments[sourceDocIndex].pages.splice(sourcePageIndex, 1);
                        this.allDocuments[targetDocIndex].pages.splice(insertionIndex, 0, pageToMove);
                    }
                }
                
                // Remove empty documents
                this.allDocuments = this.allDocuments.filter(doc => doc.pages.length > 0);
                
                // Update selected document if it was removed
                if (this.selectedDocument >= this.allDocuments.length) {
                    this.selectedDocument = -1;
                }
                
                // Clear page selection after move
                this.selectedPages.clear();
                
                this.displayAllDocuments();
                this.updateDocumentSelection();
            }

            // Upload-specific document drop zone handlers
            handleUploadDocumentDragOver(e) {
                if (this.draggedType !== 'page') return;
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
            }

            handleUploadDocumentDragEnter(e) {
                if (this.draggedType !== 'page') return;
                e.preventDefault();
                e.target.closest('.upload-document-drop-zone').classList.add('border-blue', 'bg-transparant-blue', 'text-blue');
            }

            handleUploadDocumentDragLeave(e) {
                e.target.closest('.upload-document-drop-zone').classList.remove('border-blue', 'bg-transparant-blue', 'text-blue');
            }

            handleUploadDocumentDrop(e, targetUploadId) {
                e.preventDefault();
                e.stopPropagation();
                
                if (!this.draggedData || this.draggedType !== 'page') return;
                
                // Remove drag styling
                e.target.closest('.upload-document-drop-zone').classList.remove('border-blue', 'bg-transparant-blue', 'text-blue');
                
                // Check if all pages belong to the same upload
                const firstPage = this.draggedData.pages[0];
                const firstDoc = this.allDocuments[firstPage.docIndex];
                if (firstDoc.uploadId !== targetUploadId) {
                    this.cleanupDrag();
                    return;
                }
                
                this.saveState();
                this.markAsModified();
                
                if (this.draggedData.isMultiPage) {
                    // Handle multiple pages
                    const pagesToMove = this.draggedData.pages.map(item => ({ ...item.page }));
                    const pageNumbers = pagesToMove.map(p => p.pageNumber).sort((a, b) => a - b);
                    const pageCountText = pagesToMove.length === 1 ? 'Pagina' : 'Pagina\'s';
                    const pageNumbersText = pageNumbers.length <= 3 
                        ? pageNumbers.join(', ')
                        : `${pageNumbers.slice(0, 2).join(', ')} +${pageNumbers.length - 2} meer`;
                    
                    // Get upload name and determine the new document number
                    const upload = this.uploads.find(u => u.id === targetUploadId);
                    const uploadName = upload ? upload.file_name : 'Document';
                    const existingNewDocs = this.allDocuments.filter(doc => 
                        doc.uploadId === targetUploadId && 
                        doc.name.includes('Nieuw document')
                    );
                    const newDocNumber = existingNewDocs.length + 1;
                    
                    // Create new document with all dropped pages
                    const newDocument = {
                        id: `${targetUploadId}_new_${Date.now()}`,
                        uploadId: targetUploadId,
                        name: `${uploadName} - Nieuw document ${newDocNumber}`,
                        pages: pagesToMove,
                        metadata: { type: 'custom' }
                    };
                    
                    // Group pages to remove by document index and sort by page index descending
                    const pagesToRemove = new Map();
                    this.draggedData.pages.forEach(item => {
                        if (!pagesToRemove.has(item.docIndex)) {
                            pagesToRemove.set(item.docIndex, []);
                        }
                        pagesToRemove.get(item.docIndex).push(item.pageIndex);
                    });
                    
                    // Remove pages from source documents (in reverse order to avoid index issues)
                    pagesToRemove.forEach((pageIndices, docIndex) => {
                        pageIndices.sort((a, b) => b - a); // Sort in descending order
                        pageIndices.forEach(pageIndex => {
                            if (this.allDocuments[docIndex] && this.allDocuments[docIndex].pages[pageIndex]) {
                                this.allDocuments[docIndex].pages.splice(pageIndex, 1);
                            }
                        });
                    });
                    
                    // Add new document
                    this.allDocuments.push(newDocument);
                    
                } else {
                    // Handle single page (existing logic)
                    const pageToMove = { ...this.draggedData.pages[0].page };
                    
                    // Get upload name and determine the new document number
                    const upload = this.uploads.find(u => u.id === targetUploadId);
                    const uploadName = upload ? upload.file_name : 'Document';
                    const existingNewDocs = this.allDocuments.filter(doc => 
                        doc.uploadId === targetUploadId && 
                        doc.name.includes('Nieuw document')
                    );
                    const newDocNumber = existingNewDocs.length + 1;
                    
                    // Create new document with the dropped page
                    const newDocument = {
                        id: `${targetUploadId}_new_${Date.now()}`,
                        uploadId: targetUploadId,
                        name: `${uploadName} - Nieuw document ${newDocNumber}`,
                        pages: [pageToMove],
                        metadata: { type: 'custom' }
                    };
                    
                    // Remove page from source document
                    const sourceDocIndex = this.draggedData.sourceDocIndex;
                    const sourcePageIndex = this.draggedData.sourcePageIndex;
                    this.allDocuments[sourceDocIndex].pages.splice(sourcePageIndex, 1);
                    
                    // Add new document
                    this.allDocuments.push(newDocument);
                }
                
                // Remove empty documents
                this.allDocuments = this.allDocuments.filter(doc => doc.pages.length > 0);
                
                // Update selected document if it was removed
                if (this.selectedDocument >= this.allDocuments.length - 1) {
                    this.selectedDocument = -1;
                }
                
                // Clear page selection after move
                this.selectedPages.clear();
                
                this.displayAllDocuments();
                this.updateDocumentSelection();
                this.cleanupDrag();
            }
            
            async processCreatedSplits(splitFiles) {
                // Store the split files info and redirect to verify page
                const firstSplit = splitFiles[0];
                if (firstSplit) {
                    // We need to set up the session data properly
                    // For now, just redirect to verify page
                    window.location.href = '/documents/verify';
                }
            }
        }

        // Initialize the application
        document.addEventListener('DOMContentLoaded', () => {
            new PDFSplitter();
        });
    </script>
    @endpush
</x-layout>