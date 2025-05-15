'use strict';

import Dropzone from "dropzone";
import 'dropzone/dist/dropzone.css';

document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('document-dropzone')) {
        new Dropzone('#document-dropzone', {
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 2, // MB
            acceptedFiles: ".pdf,.png,.jpg",
            init: function() {
                this.on("success", function(file, response) {
                    // Handle the response from the server
                    console.log(response);
                });
            }
        });
    }
});
