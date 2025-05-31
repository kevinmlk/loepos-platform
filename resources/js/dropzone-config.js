'use strict';

import Dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('document-dropzone')) {
        const dropzone = new Dropzone('#document-dropzone', {
            
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 2, // MB
            acceptedFiles: ".pdf,.png,.jpg",
            autoProcessQueue: false, // Prevent automatic upload
            init: function() {
                this.on("success", function(file, response) {
                    // Handle the response from the server
                    console.log(response);
                });
            }
        });

        // Add event listener to the "Upload" button
        const uploadButton = document.getElementById('form-submit');
        if (uploadButton) {
            uploadButton.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent form submission
                dropzone.processQueue(); // Manually trigger the upload
            });
        }
    }
});
