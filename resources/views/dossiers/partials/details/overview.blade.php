<h2>Facturen</h2>
<!-- Display uploaded documents -->
<table class="w-full">
    <thead>
        <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
        <th class="text-start text-caption font-regular py-2">Type</th>
        <th class="text-start text-caption font-regular py-2">Datum aankomst</th>
        <th class="text-start text-caption font-regular py-2">Einddatum</th>
    </thead>
    <tbody>
        @foreach($dossier->documents as $document)
            <tr>
                <td>{{ $document->file_name }}</td>
                <td>{{ $document->type }}</td>
                <td>{{ $document->created_at }}</td>
                <td>{{ $document->updated_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="mt-2">
</div>
