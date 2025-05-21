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
                'description' => 'Wijzig de instellingen voor uw organisatie: naam, adres, …',
                'icon' => 'bi bi-house-fill'
            ])

            @include('admin.partials.admin-card', [
                'type' => 'employees',
                'title' => 'Medewerkers',
                'description' => 'Toevoegen, bewerken en toewijzen van sociale medewerkers.',
                'icon' => 'bi bi-person-square'
            ])

            @include('admin.partials.admin-card', [
                'type' => 'clients',
                'title' => 'Cliënten',
                'description' => 'Toevoegen, bewerken en toewijzen van cliënten.',
                'icon' => 'bi bi-people-fill'
            ])
        </div>
    </section>

            {{-- Loaded partial will appear here --}}
<section id="dynamic-section" class="mt-8 border-2 border-dashed border-gray-300 p-6 rounded-xl">
    <p>Klik op een kaart om meer te zien.</p>
</section>

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
                // Inject the back button + partial
                $('#dynamic-section').html(`
                    <div class="mb-4">
                        <button id="show-admin-tools" class="btn btn-outline-secondary mb-4">
                            ← Terug naar administratie tools
                        </button>
                    </div>
                    <div>${data}</div>
                `);
            }).fail(function () {
                $('#dynamic-section').html('<p class="text-red-500">Laden mislukt.</p>');
            });
        });

         // Handle back button click (after content is loaded)
            $('#dynamic-section').on('click', '#show-admin-tools', function () {
                $('#admin-tools').slideDown();          // Show admin tools again
                $(this).parent().remove();              // Remove the back button container
            });

    });
</script>


</x-layout>

