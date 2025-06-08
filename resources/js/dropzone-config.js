'use strict';

import Dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('document-dropzone')) {
        // Get max upload size from window object (in bytes), convert to MB
        const maxFileSizeMB = window.maxUploadSizeBytes ? (window.maxUploadSizeBytes / 1024 / 1024) : 2;
        
        const dropzone = new Dropzone('#document-dropzone', {
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: maxFileSizeMB, // MB
            acceptedFiles: ".pdf,.png,.jpg",
            autoProcessQueue: false, // Prevent automatic upload
            addRemoveLinks: true,
            dictRemoveFile: "Verwijder",
            dictCancelUpload: "Annuleer",
            dictFileTooBig: "Bestand is te groot ({{filesize}}MB). Max bestandsgrootte: {{maxFilesize}}MB.",
            dictInvalidFileType: "Dit bestandstype is niet toegestaan.",
            dictResponseError: "Server antwoordde met {{statusCode}} code.",
            maxFiles: 1,
            init: function() {
                const uploadButton = document.getElementById('form-submit');
                const dzMessage = document.querySelector('.dz-message');
                
                this.on("addedfile", function(file) {
                    // Remove any existing files when a new one is added
                    if (this.files.length > 1) {
                        this.removeFile(this.files[0]);
                    }
                    // Hide the drop message when a file is added
                    if (dzMessage) {
                        dzMessage.style.display = 'none';
                    }
                    
                    // Create custom preview
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'dz-file-preview';
                    previewContainer.id = 'file-preview';
                    
                    // Get file extension
                    const ext = file.name.split('.').pop().toLowerCase();
                    const iconClass = ext === 'pdf' ? 'phosphor-file-pdf' : 'phosphor-file-image';
                    
                    // Format file size
                    let fileSize = '';
                    if (file.size >= 1024 * 1024) {
                        fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                    } else if (file.size >= 1024) {
                        fileSize = (file.size / 1024).toFixed(0) + ' KB';
                    } else {
                        fileSize = file.size + ' bytes';
                    }
                    
                    previewContainer.innerHTML = `
                        <div class="dz-file-info">
                            <div class="dz-file-icon">
                                <i class="${iconClass} text-2xl text-blue"></i>
                            </div>
                            <div class="dz-file-details">
                                <h4>${file.name}</h4>
                                <p class="flex items-center gap-2">
                                    <span class="text-blue font-semibold">${fileSize}</span>
                                    <span class="text-gray">•</span>
                                    <span class="text-gray">${ext.toUpperCase()}</span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <a href="#" class="dz-remove-file" data-dz-remove>Verwijder</a>
                            <div class="dz-progress" style="display: none;">
                                <div class="dz-upload" data-dz-uploadprogress></div>
                            </div>
                            <div class="dz-error-message" style="display: none;"></div>
                        </div>
                    `;
                    
                    // Insert after dropzone message
                    this.element.insertBefore(previewContainer, this.element.querySelector('.dropzone-buttons'));
                });
                
                this.on("removedfile", function() {
                    // Show the drop message when all files are removed
                    if (this.files.length === 0 && dzMessage) {
                        dzMessage.style.display = 'flex';
                    }
                    // Remove custom preview
                    const preview = document.getElementById('file-preview');
                    if (preview) {
                        preview.remove();
                    }
                });
                
                this.on("success", function(_file, response) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    }
                });
                
                this.on("error", function(file, errorMessage) {
                    console.error('Upload error:', errorMessage);
                    
                    // Parse error message if it's a JSON response
                    let message = 'Er is een fout opgetreden tijdens het uploaden.';
                    if (typeof errorMessage === 'string') {
                        message = errorMessage;
                    } else if (errorMessage.error) {
                        message = errorMessage.error;
                    }
                    
                    // Display error message in custom preview
                    const errorElement = document.querySelector('.dz-error-message');
                    if (errorElement) {
                        errorElement.textContent = message;
                        errorElement.style.display = 'block';
                    }
                    
                    // Hide progress bar
                    const progressBar = document.querySelector('.dz-progress');
                    if (progressBar) {
                        progressBar.style.display = 'none';
                    }
                    
                    // Re-enable upload button
                    const uploadButton = document.getElementById('form-submit');
                    if (uploadButton) {
                        uploadButton.disabled = false;
                        uploadButton.textContent = 'Upload';
                    }
                });
                
                this.on("sending", function() {
                    // Disable upload button during upload
                    if (uploadButton) {
                        uploadButton.disabled = true;
                        uploadButton.textContent = 'Uploaden...';
                    }
                    // Show progress bar
                    const progressBar = document.querySelector('.dz-progress');
                    if (progressBar) {
                        progressBar.style.display = 'block';
                    }
                });
                
                this.on("uploadprogress", function(_file, progress) {
                    // Update progress bar
                    const progressBar = document.querySelector('.dz-upload');
                    if (progressBar) {
                        progressBar.style.width = progress + '%';
                    }
                });
                
                this.on("complete", function() {
                    // Re-enable upload button after upload
                    if (uploadButton && this.getQueuedFiles().length === 0) {
                        uploadButton.disabled = false;
                        uploadButton.textContent = 'Upload';
                    }
                });
            }
        });

        // Add event listener to the "Upload" button
        const uploadButton = document.getElementById('form-submit');
        if (uploadButton) {
            uploadButton.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission
                
                if (dropzone.files.length === 0) {
                    alert('Selecteer eerst een bestand om te uploaden.');
                    return;
                }
                
                dropzone.processQueue(); // Manually trigger the upload
            });
        }
    }
});
