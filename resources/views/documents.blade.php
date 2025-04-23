<x-layout>
    {{-- Header --}}
    <x-header>Welkom terug, Sophie!</x-header>

    <section class="mt-10">
        <h2>Document toevoegen</h2>

        {{-- Upload document form --}}
        <form class="mt-5" method="POST" action="">
            <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10">
                <div class="text-center">
                    <svg class="mx-auto size-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" data-slot="icon">
                        <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" />
                    </svg>
                    <div class="mt-4 flex text-sm/6 text-gray-600">
                        <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-dark-blue focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 focus-within:outline-hidden hover:text-indigo-500">
                            <span>Bestand uploaden</span>
                            <input id="file-upload" name="file-upload" type="file" class="sr-only">
                        </label>
                        <p class="pl-1">of sleep en laat vallen</p>
                    </div>
                    <p class="text-xs/5 text-gray-600">PNG, JPG, PDF tot 10MB</p>
                </div>
            </div>
            <!-- Buttons -->
            <div class="mt-6 flex items-center justify-end gap-x-6">
                <x-ui.button type="primary">Uploaden</x-ui.button>
            </div>
        </form>
    </section>

</x-layout>
