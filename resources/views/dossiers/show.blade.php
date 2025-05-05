<x-layout>
    {{-- Header --}}
    <x-header>
        {{ $dossier->client->first_name }} {{ $dossier->client->last_name }}
        <x-slot:subText>
            {{ $dossier->client->address }}
        </x-slot:subText>
    </x-header>

    {{-- Tab navigation --}}
    <div class="flex border-b mb-4">
        <a
            href="{{ route(Route::currentRouteName(), ['dossier' => $dossier['id'], 'tab' => 'overview']) }}"
            class="px-4 py-2 {{ request('tab', 'overview') === 'overview' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500' }}"
        >
            Overzicht
        </a>

        <a
            href="{{ route(Route::currentRouteName(), ['dossier' => $dossier['id'], 'tab' => 'documents']) }}"
            class="px-4 py-2 {{ request('tab') === 'documents' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500' }}"
        >
            Documenten
        </a>

        <a
            href="{{ route(Route::currentRouteName(), ['dossier' => $dossier['id'], 'tab' => 'details   ']) }}"
            class="px-4 py-2 {{ request('tab') === 'details' ? 'border-b-2 border-blue-500 text-blue-500' : 'text-gray-500' }}"
        >
            Dossiergegevens
        </a>
    </div>

    <div class="container">
        @if (request('tab', 'overview') === 'overview')
            @include('dossiers.partials.details.overview')
        @elseif (request('tab') === 'documents')
            @include('dossiers.partials.details.documents')
        @elseif (request('tab') === 'details')
            @include('dossiers.partials.details.details')
        @endif
    </div>
</x-layout>
