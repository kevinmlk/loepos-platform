
<x-layout>
    <x-header>
        Documenten
        <x-slot:subText>
            Beheer de documenten van uw cliënten hier.
        </x-slot:subText>
    </x-header>

    <section class="flex flex-col gap-8">
        <div class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
            <div class="flex justify-between">
                <h2>Uploaded files</h2>
            </div>
            <table>
                <thead>
                    <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
                    <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
                    <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
                </thead>
                <tbody>
                    @foreach($uploads as $upload)
                    <x-shared.document-row
                        :documentId="$upload->id"
                        :fileName="$upload->file_name"
                        :filePath="$upload->file_path"
                        :createdAt="$upload->created_at"
                        :updatedAt="$upload->updated_at"
                    />
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
