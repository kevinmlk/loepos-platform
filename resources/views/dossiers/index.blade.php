<x-layout>
    {{-- Header --}}
    <x-header>
        Alle dossiers
        <x-slot:subText>
            Hier vind je een overzicht van alle dossiers.
        </x-slot:subText>
    </x-header>

    <section class="flex flex-col gap-8">
        {{-- Tab navigation --}}
        <div class="flex gap-4">
            <x-ui.tab name="overview" tab="overview">Overzicht</x-ui.tab>
            <x-ui.tab name="upload" tab="upload">Uploaden</x-ui.tab>
        </div>

        {{-- Tab content --}}
        <div class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
            @if (request('tab', 'overview') === 'overview')
                @include('dossiers.partials.overview')
            @elseif (request('tab') === 'upload')
                @include('dossiers.partials.upload')
            @endif
        </div>
    </section>

    <ul>
        @foreach ($dossiers as $dossier)
            <li>
                Dossier ID: {{ $dossier->id }} -
                Client Name: {{ optional($dossier->client)->first_name }}
            </li>
        @endforeach
    </ul>
</x-layout>
