<x-layout>
    {{-- Header --}}
    <x-header>
        Administratie
        <x-slot:subText>
            Beheer je organisatie en zijn clienten.
        </x-slot:subText>
    </x-header>

    {{-- Admin Tools --}}
    <div class="flex flex-wrap gap-3">
        @include('admin.partials.admin-card', [
            'href' => '/admin/organisation',
            'title' => 'Organisatie instellingen',
            'description' => 'Wijzig de instellingen voor uw organisatie zoals de naam, adres, …',
            'icon' => 'bi bi-house-door-fill'
        ])

        @include('admin.partials.admin-card', [
            'href' => '/admin/employees',
            'title' => 'Medewerkers',
            'description' => 'Toevoegen, bewerken en toewijzen van sociale medewerkers.',
            'icon' => 'bi bi-person-badge-fill'
        ])

        @include('admin.partials.admin-card', [
            'href' => '/admin/clients',
            'title' => 'Cliënten',
            'description' => 'Toevoegen, bewerken en toewijzen van cliënten.',
            'icon' => 'bi bi-people-fill'
        ])
    </div>


</x-layout>
