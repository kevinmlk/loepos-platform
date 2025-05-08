<h2>Alle documenten</h2>
<!-- Display uploaded documents -->
<table>
    <thead>
        <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
        <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
        <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
    </thead>
    <tbody>
    @foreach($documents as $document)
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

<div class="mt-2">
    {{ $documents->links() }}
</div>
