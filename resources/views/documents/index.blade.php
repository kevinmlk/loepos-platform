
<x-layout>
    <x-header>
        Documenten
        <x-slot:subText>
            Beheer de documenten van uw cliënten hier.
        </x-slot:subText>
    </x-header>

    {{-- Tab navigation --}}
    <div class="flex gap-4">
        <a
            href="/documents"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            Overzicht
        </a>

        <a
            href="/upload/create"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Upload
        </a>

        <a
            href="/uploads"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            AI queue
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
