<x-layout>
    {{-- Header --}}
    <x-headerAdmin>
        Administratie
        <x-slot:subText>
            Beheer je organisatie en zijn clienten.
        </x-slot:subText>
    </x-headerAdmin>

    {{-- Admin Tools --}}
    <section class="border-2 border-light-gray rounded-xl p-6">
        <h2>Administratie tools</h2>

        <div class="flex flex-wrap gap-3 mt-4">
            @include('admin.partials.admin-card', [
                'href' => '/admin/organisation',
                'title' => 'Organisatie instellingen',
                'description' => 'Wijzig de instellingen voor uw organisatie: naam, adres, …',
                'icon' => 'bi bi-house-fill'
            ])

            @include('admin.partials.admin-card', [
                'href' => '/admin/employees',
                'title' => 'Medewerkers',
                'description' => 'Toevoegen, bewerken en toewijzen van sociale medewerkers.',
                'icon' => 'bi bi-person-square'
            ])

            @include('admin.partials.admin-card', [
                'href' => '/admin/clients',
                'title' => 'Cliënten',
                'description' => 'Toevoegen, bewerken en toewijzen van cliënten.',
                'icon' => 'bi bi-people-fill'
            ])
        </div>
    </section>


</x-layout>

