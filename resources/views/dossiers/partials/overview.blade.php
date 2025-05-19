<div class="flex justify-between">
    <h2>Cliënten</h2>
    <div class="flex gap-3">
        <x-ui.button-small icon="phosphor-magnifying-glass-bold" type="secondary">Zoeken</x-ui.button-small>
        <x-ui.button-small href="/documents?tab=upload" icon="phosphor-upload-simple-bold" type="primary">Uploaden</x-ui.button-small>
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
