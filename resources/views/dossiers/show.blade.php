<x-layout>
    {{-- Header --}}
    <x-header>
        {{ $dossier->client->first_name }} {{ $dossier->client->last_name }}
        <x-slot:subText>
            {{ $dossier->client->address }}
        </x-slot:subText>
    </x-header>


</x-layout>
