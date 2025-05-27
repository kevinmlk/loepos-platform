<x-layout>
    <x-header>
        PDF Documenten Splitsen
        <x-slot:subText>
            Verwerk en splits documenten op basis van AI-analyse
        </x-slot:subText>
    </x-header>

    {{-- Pass documents data to JavaScript --}}
    <script>
        window.documentsData = @json($documents);
    </script>

    <div class="flex-1 flex gap-0 -mx-14 -mb-6 h-[calc(100vh-6rem)]">
        {{-- Left Panel - PDF Viewer --}}
        <div class="w-1/2 bg-white border-r border-light-gray flex flex-col h-full">
            {{-- Document Info Header --}}
            <div class="p-6 border-b border-light-gray bg-light-gray flex-shrink-0 h-[88px]">
                <div id="currentDocumentInfo">
                    <h3 class="text-lg font-semibold text-dark-gray mb-2">Document Preview</h3>
                    <p class="text-sm text-gray">Selecteer een document om te bekijken</p>
                </div>
            </div>

            {{-- PDF Pages Viewer --}}
            <div class="flex-1 overflow-y-auto p-6 min-h-0" id="pdfViewer">
                <div class="flex justify-center items-center h-full">
                    <div class="text-center text-gray">
                        <i class="fas fa-spinner fa-spin text-6xl mb-4"></i>
                        <p>Documenten laden...</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel - Editor --}}
        <div class="w-1/2 bg-white flex flex-col h-full relative">
            {{-- Top Section with fixed heights --}}
            <div class="flex flex-col h-full">
                {{-- Toolbar --}}
                <div class="p-6 border-b border-light-gray bg-light-gray h-[88px] flex items-center flex-shrink-0">
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
                    </div>
                </div>

                {{-- Document Rows --}}
                <div class="flex-1 overflow-y-auto p-6" style="height: calc(100% - 200px);" id="documentRows">
                    <div class="text-center text-gray mt-20">
                        <i class="fas fa-layer-group text-6xl mb-4"></i>
                        <p>Verwerk een document om splits te zien</p>
                    </div>
                </div>

                {{-- Save Button --}}
                <div class="px-6 pt-6 pb-8 border-t border-light-gray bg-light-gray h-[112px] flex-shrink-0">
                    <button id="saveButton" class="w-full bg-blue text-white py-3 px-4 rounded-xl hover:bg-dark-blue disabled:bg-gray disabled:cursor-not-allowed transition-colors font-medium" disabled>
                    <i class="fas fa-save mr-2"></i>
                    Opslaan en Verwerken
                </button>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                
                this.pdfDoc = null;
                this.currentDocumentId = null;
                this.currentDocumentIndex = 0;
                this.pages = [];
                this.documents = [];
                this.unprocessedDocuments = window.documentsData || [];
                this.selectedDocument = 0;
                this.selectedPages = new Set();
                this.history = [];
                this.historyIndex = -1;
                
                this.initializeEventListeners();
                this.setupPDFJS();
                
                // Automatically load first document if available
                if (this.unprocessedDocuments.length > 0) {
                    this.loadNextDocument();
                }
            }

            setupPDFJS() {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            }

            initializeEventListeners() {
                // Toolbar buttons
                document.getElementById('undoBtn').addEventListener('click', () => this.undo());
                document.getElementById('redoBtn').addEventListener('click', () => this.redo());
                document.getElementById('deleteBtn').addEventListener('click', () => this.deleteSelected());
                document.getElementById('rotateBtn').addEventListener('click', () => this.rotateSelected());
                document.getElementById('cutBtn').addEventListener('click', () => this.cutToNewDocument());
                document.getElementById('saveButton').addEventListener('click', () => this.saveDocuments());
            }

            async loadNextDocument() {
                if (this.currentDocumentIndex >= this.unprocessedDocuments.length) {
                    this.showNoMoreDocuments();
                    return;
                }

                const currentDoc = this.unprocessedDocuments[this.currentDocumentIndex];
                this.currentDocumentId = currentDoc.id;

                // Update document info
                const infoDiv = document.getElementById('currentDocumentInfo');
                infoDiv.innerHTML = `
                    <h3 class="text-lg font-semibold text-dark-gray mb-2">${currentDoc.file_name}</h3>
                    <p class="text-sm text-gray">Document ${this.currentDocumentIndex + 1} van ${this.unprocessedDocuments.length}</p>
                `;

                try {
                    // Load PDF
                    const pdfPath = '/storage/' + currentDoc.file_path;
                    const loadingTask = pdfjsLib.getDocument(pdfPath);
                    this.pdfDoc = await loadingTask.promise;
                    this.pages = [];
                    
                    // Extract all pages as images
                    for (let i = 1; i <= this.pdfDoc.numPages; i++) {
                        const pageImage = await this.renderPageToImage(i);
                        this.pages.push({
                            pageNumber: i,
                            imageBlob: pageImage,
                            rotation: 0
                        });
                    }

                    this.displayPDFPages();
                    
                    // Automatically process the document
                    await this.processPDF(currentDoc);
                    
                } catch (error) {
                    console.error('Error loading PDF:', error);
                    alert('Fout bij het laden van het PDF bestand');
                }
            }

            showNoMoreDocuments() {
                const viewer = document.getElementById('pdfViewer');
                viewer.innerHTML = `
                    <div class="flex justify-center items-center h-full">
                        <div class="text-center text-gray">
                            <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                            <p class="text-lg font-semibold">Alle documenten zijn verwerkt!</p>
                        </div>
                    </div>
                `;

                const documentRows = document.getElementById('documentRows');
                documentRows.innerHTML = `
                    <div class="text-center text-gray mt-20">
                        <i class="fas fa-check-circle text-6xl mb-4 text-green-500"></i>
                        <p>Geen documenten meer om te verwerken</p>
                    </div>
                `;
            }


            async renderPageToImage(pageNumber) {
                const page = await this.pdfDoc.getPage(pageNumber);
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
                    
                    if (!jsonData || !jsonData.content || !jsonData.content.documents || !Array.isArray(jsonData.content.documents)) {
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
                    }
                    
                    this.documents = this.createDocumentsFromJSON(jsonData, docData.file_name);
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

            displayDocuments() {
                const container = document.getElementById('documentRows');
                container.innerHTML = '';

                this.documents.forEach((doc, index) => {
                    const docRow = this.createDocumentRow(doc, index);
                    container.appendChild(docRow);
                });

                if (this.documents.length > 0 && this.selectedDocument >= this.documents.length) {
                    this.selectedDocument = 0;
                }
                
                this.updateToolbarState();
            }

            createDocumentRow(documentData, index) {
                const row = document.createElement('div');
                row.className = `document-row border rounded-lg mb-4 p-4 cursor-pointer transition-all ${
                    index === this.selectedDocument ? 'bg-transparant-blue border-blue' : 'bg-white border-gray hover:bg-light-gray'
                }`;
                row.dataset.documentIndex = index;

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
                thumbnailContainer.className = 'flex flex-wrap gap-2';

                documentData.pages.forEach((page, pageIndex) => {
                    const thumbnail = this.createThumbnail(page, index, pageIndex);
                    thumbnailContainer.appendChild(thumbnail);
                });

                row.appendChild(header);
                row.appendChild(thumbnailContainer);

                row.addEventListener('click', (e) => {
                    if (!e.target.closest('.thumbnail')) {
                        this.selectDocument(index);
                    }
                });

                return row;
            }

            createThumbnail(page, docIndex, pageIndex) {
                const thumbnail = document.createElement('div');
                thumbnail.className = 'thumbnail relative border border-gray rounded cursor-pointer hover:border-blue transition-all overflow-hidden';
                thumbnail.dataset.docIndex = docIndex;
                thumbnail.dataset.pageIndex = pageIndex;

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
                    this.togglePageSelection(docIndex, pageIndex);
                });

                return thumbnail;
            }

            selectDocument(index) {
                this.selectedDocument = index;
                this.selectedPages.clear();
                this.updateDocumentSelection();
                this.updateToolbarState();
                
                if (this.documents[index]) {
                    this.displaySelectedDocumentInViewer(this.documents[index]);
                }
            }

            updateDocumentSelection() {
                document.querySelectorAll('.document-row').forEach((row, index) => {
                    const pageCount = row.querySelector('.text-sm');
                    if (index === this.selectedDocument) {
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

                documentData.pages.forEach((page) => {
                    this.createPageElement(page, viewer);
                });
            }

            createPageElement(page, container) {
                const pageContainer = document.createElement('div');
                pageContainer.className = 'mb-4 border border-gray rounded-lg overflow-hidden shadow-sm';
                
                const pageHeader = document.createElement('div');
                pageHeader.className = 'bg-light-gray px-3 py-2 text-sm font-medium text-dark-gray';
                pageHeader.textContent = `Pagina ${page.pageNumber}`;
                
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
                    this.selectedPages.delete(key);
                } else {
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

            updateToolbarState() {
                const hasSelection = this.selectedPages.size > 0;
                const hasDocuments = this.documents.length > 0;
                
                document.getElementById('deleteBtn').disabled = !hasSelection;
                document.getElementById('rotateBtn').disabled = !hasSelection;
                document.getElementById('cutBtn').disabled = !hasSelection;
                document.getElementById('undoBtn').disabled = this.historyIndex <= 0;
                document.getElementById('redoBtn').disabled = this.historyIndex >= this.history.length - 1;
                document.getElementById('saveButton').disabled = !hasDocuments;
            }

            deleteSelected() {
                if (this.selectedPages.size === 0) return;
                
                this.saveState();
                
                const newDocuments = this.documents.map(doc => ({ ...doc, pages: [...doc.pages] }));
                
                this.selectedPages.forEach(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    if (newDocuments[docIndex] && newDocuments[docIndex].pages[pageIndex]) {
                        newDocuments[docIndex].pages.splice(pageIndex, 1);
                    }
                });

                // Remove empty documents
                this.documents = newDocuments.filter(doc => doc.pages.length > 0);
                this.selectedPages.clear();
                this.displayDocuments();
            }

            rotateSelected() {
                if (this.selectedPages.size === 0) return;
                
                this.saveState();
                
                this.selectedPages.forEach(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    if (this.documents[docIndex] && this.documents[docIndex].pages[pageIndex]) {
                        this.documents[docIndex].pages[pageIndex].rotation += 90;
                        if (this.documents[docIndex].pages[pageIndex].rotation >= 360) {
                            this.documents[docIndex].pages[pageIndex].rotation = 0;
                        }
                    }
                });

                // Clear selections to force redraw
                this.selectedPages.clear();
                this.displayDocuments();
                this.displaySelectedDocumentInViewer(this.documents[this.selectedDocument]);
                this.updateToolbarState();
            }

            cutToNewDocument() {
                if (this.selectedPages.size === 0) return;
                
                this.saveState();
                
                const selectedPagesArray = Array.from(this.selectedPages).map(key => {
                    const [docIndex, pageIndex] = key.split('-').map(Number);
                    return {
                        docIndex,
                        pageIndex,
                        page: this.documents[docIndex].pages[pageIndex]
                    };
                }).sort((a, b) => a.page.pageNumber - b.page.pageNumber);

                // Create new document with selected pages
                const newDocument = {
                    id: this.documents.length,
                    name: `Nieuw Document - ${selectedPagesArray.length} pagina's`,
                    pages: selectedPagesArray.map(item => ({ ...item.page })),
                    metadata: null
                };

                // Remove selected pages from original documents
                const pagesToRemove = new Map();
                selectedPagesArray.forEach(item => {
                    if (!pagesToRemove.has(item.docIndex)) {
                        pagesToRemove.set(item.docIndex, []);
                    }
                    pagesToRemove.get(item.docIndex).push(item.pageIndex);
                });

                pagesToRemove.forEach((pageIndices, docIndex) => {
                    pageIndices.sort((a, b) => b - a); // Sort in descending order
                    pageIndices.forEach(pageIndex => {
                        this.documents[docIndex].pages.splice(pageIndex, 1);
                    });
                });

                // Remove empty documents and add new document
                this.documents = this.documents.filter(doc => doc.pages.length > 0);
                this.documents.push(newDocument);
                
                this.selectedPages.clear();
                this.displayDocuments();
            }

            saveState() {
                const state = JSON.parse(JSON.stringify({
                    documents: this.documents,
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

            undo() {
                if (this.historyIndex > 0) {
                    this.historyIndex--;
                    const state = this.history[this.historyIndex];
                    this.documents = JSON.parse(JSON.stringify(state.documents));
                    this.selectedDocument = state.selectedDocument;
                    this.selectedPages.clear();
                    this.displayDocuments();
                }
            }

            redo() {
                if (this.historyIndex < this.history.length - 1) {
                    this.historyIndex++;
                    const state = this.history[this.historyIndex];
                    this.documents = JSON.parse(JSON.stringify(state.documents));
                    this.selectedDocument = state.selectedDocument;
                    this.selectedPages.clear();
                    this.displayDocuments();
                }
            }

            async saveDocuments() {
                if (this.documents.length === 0 || !this.currentDocumentId) return;
                
                const saveButton = document.getElementById('saveButton');
                saveButton.disabled = true;
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Verwerken...';
                
                try {
                    // Prepare data for saving
                    const documentsToSave = this.documents.map((doc, index) => ({
                        originalDocId: this.currentDocumentId,
                        name: doc.name,
                        pages: doc.pages.map(p => p.pageNumber),
                        metadata: doc.metadata
                    }));
                    
                    // Send to server
                    const response = await fetch('/documents/process-queue', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            documents: documentsToSave
                        })
                    });
                    
                    if (response.ok) {
                        // Success - move to next document
                        this.currentDocumentIndex++;
                        this.documents = [];
                        this.pages = [];
                        this.selectedPages.clear();
                        this.history = [];
                        this.historyIndex = -1;
                        
                        // Load next document or show completion
                        await this.loadNextDocument();
                    } else {
                        throw new Error('Server error');
                    }
                } catch (error) {
                    console.error('Error saving documents:', error);
                    alert('Er is een fout opgetreden bij het opslaan van de documenten.');
                    
                    // Re-enable save button
                    saveButton.disabled = false;
                    saveButton.innerHTML = '<i class="fas fa-save mr-2"></i>Opslaan en Verwerken';
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