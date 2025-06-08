<x-layout>
    {{-- Header --}}
    <x-header>
        Alle dossiers
        <x-slot:subText>
            Hier vind u een overzicht van alle dossiers.
        </x-slot:subText>
    </x-header>

    {{-- Tab navigation --}}
    <!-- <div class="flex gap-4">
        <a
            href="/dossiers"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            Overzicht
        </a>

        <a
            href="/tasks"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Taken
        </a>
    </div> -->

    {{-- Section to display the dossiers and inbox --}}
    <section class="border-2 border-light-gray rounded-xl p-6">
        <div class="flex justify-between">
            <h2>Cliënten</h2>
            <div class="flex gap-3">
                <x-ui.button-small icon="phosphor-magnifying-glass-bold" type="secondary">Zoeken</x-ui.button-small>
                <x-ui.button-small href="/upload" icon="phosphor-upload-simple-bold" type="primary">Uploaden</x-ui.button-small>
            </div>
        </div>
        <table class="w-full">
            <thead>
                <th class="text-start text-caption font-regular py-2">Naam</th>
                <th class="text-start text-caption font-regular py-2 px-6">Status</th>
                <th class="text-start text-caption font-regular py-2 px-6">GSM-nummer</th>
                <th class="text-start text-caption font-regular py-2 px-6">E-mailadres</th>
            </thead>
            <tbody>
                @foreach($dossiers as $dossier)
                <x-shared.dossier-row
                    :dossierId="$dossier->id"
                    :firstName="$dossier->client->first_name"
                    :lastName="$dossier->client->last_name"
                    :status="$dossier->status"
                    :phone="$dossier->client->phone"
                    :email="$dossier->client->email"
                />
                @endforeach
            </tbody>
        </table>

        <div class="mt-2">
            {{ $dossiers->links() }}
        </div>
    </section>
</x-layout>
