
<x-layout>
    {{-- Header --}}
    <x-header>
        Alle documenten
        <x-slot:subText>
            Beheer de documenten van uw cliënten hier.
        </x-slot:subText>
    </x-header>
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

</x-layout>
