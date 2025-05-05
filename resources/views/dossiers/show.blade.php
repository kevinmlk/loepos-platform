<x-layout>
    {{-- Header --}}
    <x-header>
        {{ $dossier->client->first_name }} {{ $dossier->client->last_name }}
        <x-slot:subText>
            {{ $dossier->client->address }}
        </x-slot:subText>
    </x-header>
    <div class="container">
        <h2>Documents</h2>
        <p><strong>Client Name:</strong> {{ $dossier->client->first_name }} {{ $dossier->client->last_name }}</p>
            <p><strong>Status:</strong> {{ $dossier->status }}</p>
            <p><strong>Phone:</strong> {{ $dossier->client->phone }}</p>
            <p><strong>Email:</strong> {{ $dossier->client->email }}</p>

            <h2>Documents</h2>
            <ul class="">
                @if ($dossier->documents->isEmpty())
                    <li>No documents found.</li>
                @else
                    @foreach ($dossier->documents as $document)
                        <li>
                            <a href="{{ asset('storage/' . $document->file_path )}}" target="_blank">{{ $document->file_name }}</a>
                        </li>
                    @endforeach
                @endif
            </ul>
    </div>
</x-layout>
