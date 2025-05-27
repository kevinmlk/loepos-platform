# PDF File Splitter

A web-based PDF viewer and editor that allows you to split PDF documents based on JSON configuration and manage individual pages.

## Features

- **PDF Upload & Processing**: Upload PDF files and extract pages as image blobs
- **JSON Configuration**: Define document splits using JSON format
- **Visual Editor**: 
  - View PDF pages as large images on the left panel
  - Manage document rows with thumbnail previews on the right panel
- **Page Management**:
  - Select multiple pages across documents
  - Delete selected pages
  - Rotate pages (90° increments)
  - Cut pages to create new documents
- **History**: Undo/Redo functionality for all operations

## Usage

1. **Upload PDF**: Select a PDF file using the file input
2. **Paste JSON Config**: Add your JSON configuration in the textarea
3. **Process**: Click "Process PDF" to split the document according to JSON config
4. **Edit**: Use the toolbar buttons to manipulate pages:
   - Click document rows to view them in the left panel
   - Click thumbnails to select pages
   - Use toolbar buttons (undo, redo, delete, rotate, cut)

## JSON Format

```json
{
    "success": true,
    "fileType": "application/pdf",
    "numPages": 4,
    "content": {
        "totalPages": 2,
        "uniquePages": 2,
        "documents": [
            {
                "startPage": 1,
                "endPage": 1,
                "sender": { ... },
                "receiver": { ... },
                "documentDetails": { ... }
            }
        ]
    }
}
```

## Controls

- **Left Panel**: Large page viewer for selected document
- **Right Panel**: 
  - Top toolbar with edit controls
  - Document rows with thumbnail previews
- **Selection**: Click thumbnails to select/deselect pages
- **Cut Operation**: Select pages and click cut to create new document starting from selection

## Technical Details

- Uses PDF.js for PDF processing
- Tailwind CSS for styling
- Font Awesome for icons
- Pure vanilla JavaScript (no frameworks)
- Client-side only processing