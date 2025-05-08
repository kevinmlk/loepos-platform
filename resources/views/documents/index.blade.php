
<x-layout>
    <x-header>
        Documenten
        <x-slot:subText>
            Beheer de documenten van uw cliënten hier.
        </x-slot:subText>
    </x-header>

    {{-- Tab navigation --}}
    <div class="flex gap-4">
        <x-ui.tab name="overview" tab="overview">Overzicht</x-ui.tab>
        <x-ui.tab name="upload" tab="upload">Uploaden</x-ui.tab>
    </div>

    <section class="flex flex-col gap-8">
        {{-- Tab content --}}
        <div class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
            @if (request('tab', 'overview') === 'overview')
                @include('documents.partials.overview')
            @elseif (request('tab') === 'upload')
                @include('documents.partials.upload')
            @endif
        </div>
    </section>
</x-layout>
