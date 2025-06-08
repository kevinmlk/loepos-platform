import "./bootstrap";
import "fslightbox";

// Make fslightbox available globally
window.refreshFsLightbox = window.refreshFsLightbox || function() {
    if (window.fsLightbox) {
        new FsLightbox().props.loadOnlyCurrentSource = true;
    }
};
