<x-layout>
    {{-- Header --}}
    <x-header>
        {{ $dossier->client->first_name }} {{ $dossier->client->last_name }}
        <x-slot:subText>
            {{ $dossier->client->address }}
        </x-slot:subText>
    </x-header>

    {{-- Tab navigation --}}
    <div class="flex gap-4">
        <a
            href="{{ route(Route::currentRouteName(), ['dossier' => $dossier['id'], 'tab' => 'overview']) }}"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium {{ request('tab', 'overview') === 'overview' ? 'bg-blue text-white' : 'hover:bg-blue hover:text-white focus:outline-none' }}"
        >
            Overzicht
        </a>

        <a
            href="{{ route(Route::currentRouteName(), ['dossier' => $dossier['id'], 'tab' => 'documents']) }}"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium {{ request('tab') === 'documents' ? 'bg-blue text-white' : 'hover:bg-blue hover:text-white focus:outline-none' }}"
        >
            Documenten
        </a>

        <a
            href="{{ route(Route::currentRouteName(), ['dossier' => $dossier['id'], 'tab' => 'details   ']) }}"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium {{ request('tab') === 'details' ? 'bg-blue text-white' : 'hover:bg-blue hover:text-white focus:outline-none' }}"
        >
            Dossiergegevens
        </a>
    </div>
    @if (request('tab', 'overview') === 'overview')
        @include('dossiers.partials.details.overview')
    @elseif (request('tab') === 'documents')
        @include('dossiers.partials.details.documents')
    @elseif (request('tab') === 'details')
        @include('dossiers.partials.details.details')
    @endif
</x-layout>
