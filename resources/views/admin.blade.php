<x-layout>
    {{-- Header --}}
    <x-header>
        Welkom terug, {{ Auth()->user()->first_name }}!
        <x-slot:subText>
            Beheer je cliënten en hun dossiers.
        </x-slot:subText>
    </x-header>

    <p>Administratie pagina en enkel voor admin users pagina</p>
</x-layout>
