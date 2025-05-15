<x-layout>
    {{-- Header --}}
    <x-header>
        Welkom terug, {{ Auth()->user()->first_name }}!
        <x-slot:subText>
            Beheer je cliënten en hun dossiers.
        </x-slot:subText>
    </x-header>

    <p>Administratie pagina en enkel voor admin users pagina</p>

    {{-- Admin Tools --}}
    <div class="flex flex-wrap gap-3">
        @include('admin.partials.admin-card', [
            'href' => '/admin/organisatie',
            'title' => 'Organisatie instellingen',
            'description' => 'Wijzig de instellingen voor uw organisatie zoals de naam, adres, …',
            'icon' => 'bi bi-house-door-fill'
        ])

        @include('admin.partials.admin-card', [
            'href' => '/admin/medewerkers',
            'title' => 'Medewerkers',
            'description' => 'Toevoegen, bewerken en toewijzen van sociale medewerkers.',
            'icon' => 'bi bi-person-badge-fill'
        ])

        @include('admin.partials.admin-card', [
            'href' => '/admin/clienten',
            'title' => 'Cliënten',
            'description' => 'Toevoegen, bewerken en toewijzen van cliënten.',
            'icon' => 'bi bi-people-fill'
        ])
    </div>


</x-layout>
