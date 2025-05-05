<x-layout>
    {{-- Header --}}
    <x-header>
        Alle dossiers
        <x-slot:subText>
            Hier vind je een overzicht van alle dossiers.
        </x-slot:subText>
    </x-header>

    {{-- Tab navigation --}}
    <div class="flex gap-4">
        <x-ui.tab name="overview" tab="overview">Overzicht</x-ui.tab>
        <x-ui.tab name="inbox" tab="inbox">Inbox</x-ui.tab>
    </div>

    {{-- Section to display the dossiers and inbox --}}
    <section class="border-2 border-light-gray rounded-xl p-6">
        {{-- Tab content --}}
        <div class="">
            @if (request('tab', 'overview') === 'overview')
                @include('dossiers.partials.overview')
            @elseif (request('tab') === 'inbox')
                @include('dossiers.partials.inbox')
            @endif
        </div>
    </section>
</x-layout>
