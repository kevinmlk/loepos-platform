<h2>{{ count($dossiers) }} cliënten</h2>
<!-- Display uploaded documents -->
<table class="w-full">
    <thead>
        <th class="text-start text-caption font-regular py-2">Naam</th>
        <th class="text-start text-caption font-regular py-2">Status</th>
        <th class="text-start text-caption font-regular py-2">GSM-nummer</th>
        <th class="text-start text-caption font-regular py-2">E-mailadres</th>
    </thead>
    <tbody>
    @foreach($dossiers as $dossier)
        <x-shared.dossier-row
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
