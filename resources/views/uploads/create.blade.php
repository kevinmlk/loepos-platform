<x-layout>
    {{-- Header --}}
    <x-header>
        Uploaden
        <x-slot:subText>
            Upload nieuwe documenten hier.
        </x-slot:subText>
    </x-header>

    <div class="flex gap-4">
        <a
            href="/documents"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Overzicht
        </a>

        <a
            href="/upload/create"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            Upload
        </a>
    </div>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
        <h2>Documenten uploaden</h2>

        {{-- Upload document form --}}
        <form action="/upload" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 dropzone" id="document-dropzone">
            @csrf
            <div class="dz-message flex flex-col">
                <x-phosphor-image-thin class="h-30 text-blue" />
                <span>Sleep je documenten hier, <br>of klik om een bestand te kiezen.</span>
            </div>

            <div class="dropzone-buttons">
                <x-ui.button type="tertiary" href="/upload/create">Annuleren</x-ui.button>
                <x-ui.button type="primary" id="form-submit">Upload</x-ui.button>
            </div>
        </form>

        @vite(['resources/js/dropzone-config.js'])
    </section>
</x-layout>
