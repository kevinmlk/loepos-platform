<x-layout>
    {{-- Header --}}
    <x-headerAdmin>
        Administratie
        <x-slot:subText>
            Beheer je organisatie en zijn clienten.
        </x-slot:subText>
    </x-headerAdmin>

    {{-- Admin Tools --}}
    <section id="admin-tools" class="border-2 border-light-gray rounded-xl p-6">
        <h2>Administratie tools</h2>

        <div class="flex flex-wrap gap-3 mt-4">
            @include('admin.partials.admin-card', [
                'type' => 'organisation',
                'title' => 'Organisatie instellingen',
                'description' => 'Wijzig de instellingen voor uw organisatie: naam, adres, nummer, …',
                'icon' => 'bi bi-house-fill'
            ])

            @include('admin.partials.admin-card', [
                'type' => 'employees',
                'title' => 'Medewerkers',
                'description' => 'Toevoegen, bewerken en toewijzen van sociale medewerkers in je organisatie.',
                'icon' => 'bi bi-person-square'
            ])

            @include('admin.partials.admin-card', [
                'type' => 'clients',
                'title' => 'Cliënten',
                'description' => 'Toevoegen, bewerken van cliënten en toewijzen aan een medewerker.',
                'icon' => 'bi bi-people-fill'
            ])
        </div>
    </section>

    {{-- Back button template (hidden) --}}
    <div id="back-button-template" class="hidden">
        <x-ui.button href="#" class="mb-4 w-max float-left mr-4" id="show-admin-tools" type="secondary">
            ← Terug naar administratie tools
        </x-ui.button>

        <x-ui.button href="/admin/employee" class="mb-4 w-max" type="primary">
        Medewerker toevoegen
        </x-ui.button>
    </div>

    {{-- Loaded partial will appear here --}}
    <section id="dynamic-section" class="mt-8 border-2 border-dashed border-gray-300 p-6 rounded-xl">
        <p>Klik op een kaart om meer te zien.</p>
    </section>


</x-layout>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function () {
        $('.admin-card-trigger').click(function () {
            const type = $(this).data('type'); // 'clients', 'employees', etc.

            // Slide up admin tools
            $('#admin-tools').slideUp();

            // Show loading message
            $('#dynamic-section').html('<p>Bezig met laden...</p>');


            // Load the corresponding partial view
            $.get(`/admin/section/${type}`, function (data) {
                const backButton = $('#back-button-template').html(); // Load pre-rendered button

                $('#dynamic-section').html(`
                    <div id="dynamic-wrapper">
                        ${backButton}
                        <div>${data}</div>
                    </div>
                `);
            }).fail(function () {
                $('#dynamic-section').html('<p class="text-red-500">Laden mislukt.</p>');
            });
        });

        // Delegated event for back button
        $('#dynamic-section').on('click', '#show-admin-tools', function (e) {

            $('#admin-tools').slideDown();
            $('#dynamic-wrapper').remove(); // remove back button + loaded content
        });
    });
</script>
