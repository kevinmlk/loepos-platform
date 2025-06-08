<x-layout>
    {{-- Custom header without buttons --}}
    <header class="flex justify-between">
        <div>
            <h1 class="text-4xl font-bold">Uploaden</h1>
            <p class="mt-1 text-dark-gray">Upload nieuwe documenten hier.</p>
        </div>
    </header>

    <div class="flex gap-4">
        <a
            href="/documents"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Overzicht
        </a>

        <a
            href="/upload"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            Upload
        </a>

        <a
            href="/queue"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium relative"
        >
            AI queue
            @if($queueCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center">
                    {{ $queueCount }}
                </span>
            @endif
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
