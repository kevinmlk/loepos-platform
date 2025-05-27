class PDFSplitter {
    constructor() {
        // Check if we're in a browser environment
        if (typeof document === 'undefined') {
            console.error('This application must run in a browser environment');
            return;
        }
        
        if (typeof document.createElement !== 'function') {
            console.error('document.createElement is not available');
            return;
        }
        
        this.pdfDoc = null;
        this.pages = [];
        this.documents = [];
        this.selectedDocument = 0;
        this.selectedPages = new Set();
        this.history = [];
        this.historyIndex = -1;
        
        this.initializeEventListeners();
        this.setupPDFJS();
    }

    setupPDFJS() {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    initializeEventListeners() {
        const pdfUpload = document.getElementById('pdfUpload');
        const processButton = document.getElementById('processButton');
        const jsonInput = document.getElementById('jsonInput');

        pdfUpload.addEventListener('change', (e) => this.handleFileUpload(e));
        processButton.addEventListener('click', () => this.processPDF());
        jsonInput.addEventListener('input', () => this.validateJSON());

        // Toolbar buttons
        document.getElementById('undoBtn').addEventListener('click', () => this.undo());
        document.getElementById('redoBtn').addEventListener('click', () => this.redo());
        document.getElementById('deleteBtn').addEventListener('click', () => this.deleteSelected());
        document.getElementById('rotateBtn').addEventListener('click', () => this.rotateSelected());
        document.getElementById('cutBtn').addEventListener('click', () => this.cutToNewDocument());
    }

    async handleFileUpload(event) {
        const file = event.target.files[0];
        if (!file || file.type !== 'application/pdf') {
            alert('Please select a valid PDF file');
            return;
        }

        try {
            const arrayBuffer = await file.arrayBuffer();
            this.pdfDoc = await pdfjsLib.getDocument(arrayBuffer).promise;
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
            this.enableProcessButton();
        } catch (error) {
            console.error('Error loading PDF:', error);
            alert('Error loading PDF file');
        }
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
            const pageContainer = document.createElement('div');
            pageContainer.className = 'mb-4 border border-gray-300 rounded-lg overflow-hidden shadow-sm';
            
            const pageHeader = document.createElement('div');
            pageHeader.className = 'bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700';
            pageHeader.textContent = `Page ${page.pageNumber}`;
            
            const img = document.createElement('img');
            img.src = URL.createObjectURL(page.imageBlob);
            img.className = 'w-full h-auto';
            img.style.transform = `rotate(${page.rotation}deg)`;
            
            pageContainer.appendChild(pageHeader);
            pageContainer.appendChild(img);
            viewer.appendChild(pageContainer);
        });
    }

    validateJSON() {
        console.log('validateJSON called');
        console.log('document object:', typeof document);
        console.log('document.getElementById:', typeof document.getElementById);
        
        try {
            const jsonInput = document.getElementById('jsonInput');
            const processButton = document.getElementById('processButton');
            
            if (!jsonInput || !processButton) {
                console.error('Elements not found:', { jsonInput, processButton });
                return false;
            }
            
            const jsonData = JSON.parse(jsonInput.value);
            if (jsonData.content && jsonData.content.documents && Array.isArray(jsonData.content.documents)) {
                processButton.disabled = false;
                return true;
            } else {
                processButton.disabled = true;
                return false;
            }
        } catch (error) {
            console.error('Error in validateJSON:', error);
            const processButton = document.getElementById('processButton');
            if (processButton) {
                processButton.disabled = true;
            }
            return false;
        }
    }

    enableProcessButton() {
        const processButton = document.getElementById('processButton');
        const jsonInput = document.getElementById('jsonInput');
        
        if (this.pages.length > 0 && jsonInput.value.trim()) {
            this.validateJSON();
        }
    }

    processPDF() {
        const jsonInput = document.getElementById('jsonInput');
        
        if (!this.pages || this.pages.length === 0) {
            alert('Please upload a PDF file first');
            return;
        }
        
        try {
            const jsonData = JSON.parse(jsonInput.value);
            console.log('Parsed JSON:', jsonData);
            
            if (!jsonData.content || !jsonData.content.documents || !Array.isArray(jsonData.content.documents)) {
                throw new Error('Invalid JSON structure: missing content.documents array');
            }
            
            this.documents = this.createDocumentsFromJSON(jsonData);
            this.displayDocuments();
            if (this.documents.length > 0) {
                this.selectDocument(0);
            }
            this.saveState();
        } catch (error) {
            console.error('Error processing JSON:', error);
            alert(`Error: ${error.message}`);
        }
    }

    createDocumentsFromJSON(jsonData) {
        const documents = [];
        
        jsonData.content.documents.forEach((doc, index) => {
            if (!doc.startPage || !doc.endPage) {
                throw new Error(`Document ${index + 1} missing startPage or endPage`);
            }
            
            const documentPages = [];
            for (let i = doc.startPage; i <= doc.endPage; i++) {
                if (this.pages[i - 1]) {
                    documentPages.push({
                        ...this.pages[i - 1],
                        originalIndex: i - 1
                    });
                } else {
                    console.warn(`Page ${i} not found in PDF (document has ${this.pages.length} pages)`);
                }
            }
            
            documents.push({
                id: index,
                name: `Document ${index + 1}`,
                pages: documentPages,
                metadata: doc
            });
        });

        console.log('Created documents:', documents);
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
    }

    createDocumentRow(documentData, index) {
        const row = document.createElement('div');
        row.className = `document-row border border-gray-300 rounded-lg mb-4 p-4 cursor-pointer ${
            index === this.selectedDocument ? 'bg-blue-50 border-blue-500' : 'bg-white hover:bg-gray-50'
        }`;
        row.dataset.documentIndex = index;

        const header = document.createElement('div');
        header.className = 'flex items-center justify-between mb-3';
        
        const title = document.createElement('h3');
        title.className = 'text-lg font-semibold text-gray-800';
        title.textContent = documentData.name;
        
        const pageCount = document.createElement('span');
        pageCount.className = 'text-sm text-gray-500';
        pageCount.textContent = `${documentData.pages.length} pages`;
        
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
        thumbnail.className = 'thumbnail relative border border-gray-300 rounded cursor-pointer hover:border-blue-500';
        thumbnail.dataset.docIndex = docIndex;
        thumbnail.dataset.pageIndex = pageIndex;

        const img = document.createElement('img');
        img.src = URL.createObjectURL(page.imageBlob);
        img.className = 'w-16 h-20 object-cover rounded';
        img.style.transform = `rotate(${page.rotation}deg)`;

        const pageNumber = document.createElement('div');
        pageNumber.className = 'absolute top-0 left-0 bg-black bg-opacity-75 text-white text-xs px-1 rounded-br';
        pageNumber.textContent = page.pageNumber;

        thumbnail.appendChild(img);
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
            if (index === this.selectedDocument) {
                row.className = row.className.replace('bg-white hover:bg-gray-50', 'bg-blue-50 border-blue-500');
            } else {
                row.className = row.className.replace('bg-blue-50 border-blue-500', 'bg-white hover:bg-gray-50');
            }
        });
    }

    displaySelectedDocumentInViewer(documentData) {
        const viewer = document.getElementById('pdfViewer');
        viewer.innerHTML = '';

        documentData.pages.forEach((page) => {
            const pageContainer = document.createElement('div');
            pageContainer.className = 'mb-4 border border-gray-300 rounded-lg overflow-hidden shadow-sm';
            
            const pageHeader = document.createElement('div');
            pageHeader.className = 'bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700';
            pageHeader.textContent = `Page ${page.pageNumber}`;
            
            const img = document.createElement('img');
            img.src = URL.createObjectURL(page.imageBlob);
            img.className = 'w-full h-auto';
            img.style.transform = `rotate(${page.rotation}deg)`;
            
            pageContainer.appendChild(pageHeader);
            pageContainer.appendChild(img);
            viewer.appendChild(pageContainer);
        });
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
                thumb.classList.add('ring-2', 'ring-blue-500', 'bg-blue-100');
            } else {
                thumb.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-100');
            }
        });
    }

    updateToolbarState() {
        const hasSelection = this.selectedPages.size > 0;
        
        document.getElementById('deleteBtn').disabled = !hasSelection;
        document.getElementById('rotateBtn').disabled = !hasSelection;
        document.getElementById('cutBtn').disabled = !hasSelection;
        document.getElementById('undoBtn').disabled = this.historyIndex <= 0;
        document.getElementById('redoBtn').disabled = this.historyIndex >= this.history.length - 1;
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

        this.displayDocuments();
        this.displaySelectedDocumentInViewer(this.documents[this.selectedDocument]);
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
            name: `Document ${this.documents.length + 1}`,
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
}

// Initialize the application
document.addEventListener('DOMContentLoaded', () => {
    new PDFSplitter();
});