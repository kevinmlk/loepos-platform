
<x-layout>
    {{-- Custom header without buttons --}}
    <header class="flex justify-between">
        <div>
            <h1 class="text-4xl font-bold">Documenten</h1>
            <p class="mt-1 text-dark-gray">Beheer de documenten van uw cliënten hier.</p>
        </div>
    </header>

    {{-- Tab navigation --}}
    <div class="flex gap-4">
        <a
            href="/documents"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            Overzicht
        </a>

        <a
            href="/upload"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Uploaden
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

    <section class="flex flex-col gap-8">
        {{-- Tab content --}}
        <div class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
            <div class="flex justify-between">
                <h2>Alle documenten</h2>
                <div class="flex gap-3">
                    <x-ui.button-small icon="phosphor-magnifying-glass-bold" type="secondary">Zoeken</x-ui.button-small>
                </div>
            </div>
            <table>
                <thead>
                    <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
                    <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
                    <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
                </thead>
                <tbody>
                @foreach($documents as $document)
                    <x-shared.document-row
                        :documentId="$document->id"
                        :fileName="$document->file_name"
                        :filePath="$document->file_path"
                        :createdAt="$document->created_at"
                        :updatedAt="$document->updated_at"
                    />
                @endforeach
                </tbody>
            </table>

            <div class="mt-2">
                {{-- $documents->links() --}}
            </div>
        </div>
    </section>
</x-layout>
