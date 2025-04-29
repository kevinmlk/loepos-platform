<h2>Document uploaden</h2>

{{-- Upload document form --}}
<form action="/document" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 dropzone" id="document-dropzone">
    @csrf
    <div class="dz-message flex flex-col">
        <x-phosphor-image-thin class="h-30 text-blue" />
        <span>Drop files here to upload</span>
    </div>

    <div class="flex justify-end gap-4">
        <x-ui.button type="tertiary" href="/documents?tab=upload">Annuleren</x-ui.button>
        <x-ui.button type="primary" id="form-submit">Upload</x-ui.button>
    </div>
</form>

@vite(['resources/js/dropzone-config.js'])
