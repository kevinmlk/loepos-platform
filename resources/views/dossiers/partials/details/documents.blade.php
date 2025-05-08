<article class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
    <h2>Documenten</h2>
    <!-- Display uploaded documents -->
    <table>
        <thead>
            <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
            <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
            <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
        </thead>
        <tbody>
        @foreach($dossier->documents as $document)
            <x-shared.document-row
                :fileName="$document->file_name"
                :filePath="$document->file_path"
                :createdAt="$document->created_at"
                :updatedAt="$document->updated_at"
            />
            <!-- <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">{{ $document->file_name }}</a>
            ({{ $document->mime_type }}) -->
        @endforeach
        </tbody>
    </table>
</article>
