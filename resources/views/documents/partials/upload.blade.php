<h2>Document uploaden</h2>

{{-- Upload document form --}}
<form action="/document" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
    @csrf

    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
        <div class="text-center flex flex-col">
            <x-phosphor-image-light class="h-20 text-black" />
            <div class="mt-4 flex flex-col text-center text-sm/6 text-gray-600">
                <label for="file" class="flex flex-col font-medium text-blue ">
                    <span>Upload een bestand</span>
                    <input id="file" name="file" type="file" class="sr-only" accept=".pdf,image/*" required>
                </label>
                <p class="pl-1">of sleep je documenten hier</p>
            </div>
            <p class="text-xs/5 text-gray-600">PDF, PNG, JPG tot 10MB</p>
        </div>
    </div>

    <div class="flex justify-end gap-4">
        <x-ui.button type="tertiary" href="/documents?tab=upload">Annuleren</x-ui.button>
        <x-ui.button type="primary">Upload</x-ui.button>
    </div>
</form>
