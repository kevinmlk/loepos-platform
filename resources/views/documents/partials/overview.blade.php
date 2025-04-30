<h2>Alle documenten</h2>
<!-- Display uploaded documents -->
<table>
    <thead>
        <th class="px-4 py-2">Bestandsnaam</th>
        <th class="px-4 py-2">Mime type</th>
        <th class="px-4 py-2">Datum geüpload</th>
        <th class="px-4 py-2">Laatst bewerkt</th>
    </thead>
    <tbody>
    @foreach($documents as $document)
        <x-shared.document-row
            :fileName="$document->file_name"
            :mimeType="$document->mime_type"
            :createdAt="$document->created_at"
            :updatedAt="$document->updated_at"
        />
        <!-- <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">{{ $document->file_name }}</a>
        ({{ $document->mime_type }}) -->
    </tbody>
    @endforeach
</table>
