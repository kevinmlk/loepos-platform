<x-layout>
    {{-- Header --}}
    <x-header>
        Welkom terug, {{ Auth()->user()->first_name }}!
        <x-slot:subText>
            Traceer en beheer je cliënten en hun dossiers.
        </x-slot:subText>
    </x-header>

    <section clas="">
        <p>Home pagina</p>

    </section>
</x-layout>
