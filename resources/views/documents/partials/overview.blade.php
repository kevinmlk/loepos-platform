<h2>Index page - Documents</h2>
<!-- Display uploaded documents -->
<ul>
    @foreach($documents as $document)
    <li>
        <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank">{{ $document->file_name }}</a>
        ({{ $document->mime_type }})
    </li>
    @endforeach
</ul>
