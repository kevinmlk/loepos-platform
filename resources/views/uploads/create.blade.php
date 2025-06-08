<x-layout>
    {{-- Custom header without buttons --}}
    <header class="flex justify-between">
        <div>
            <h1 class="text-4xl font-bold">Documenten</h1>
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
            Wachtrij
            @if($queueCount > 0)
                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full min-w-[1.25rem] h-5 px-1 flex items-center justify-center">
                    {{ $queueCount }}
                </span>
            @endif
        </a>
    </div>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
        <h2>Documenten uploaden</h2>

        {{-- Display errors if any --}}
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Er is een fout opgetreden!</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Upload document form --}}
        <form action="/upload" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4 dropzone" id="document-dropzone">
            @csrf
            <div class="dz-message flex flex-col items-center justify-center p-12 border-2 border-dashed border-gray-300 rounded-lg hover:border-blue-500 transition-colors cursor-pointer bg-gray-50 hover:bg-gray-100">
                <x-phosphor-image-thin class="h-30 text-blue mb-4" />
                <span class="text-lg font-medium text-gray-600 mb-2">Sleep je documenten hier</span>
                <span class="text-sm text-gray-500">of klik om een bestand te kiezen</span>
                <span class="text-xs text-gray-400 mt-4">Ondersteunde formaten: PDF, PNG, JPG (Max. 2MB)</span>
            </div>

            <div class="dropzone-buttons flex justify-end gap-3 mt-4">
                <x-ui.button type="tertiary" href="/documents">Annuleren</x-ui.button>
                <x-ui.button type="primary" id="form-submit">Upload</x-ui.button>
            </div>
        </form>

        @vite(['resources/js/dropzone-config.js'])
    </section>
</x-layout>
