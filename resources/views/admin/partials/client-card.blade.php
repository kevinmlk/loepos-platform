<div class="card text-center p-3 shadow-sm rounded" style="width: 300px; height: 200px; border: none;">
    <div class="d-flex justify-content-between">
        <i class="bi bi-person-badge" style="font-size: 1.2rem;"></i>
        <i class="bi bi-three-dots-vertical"></i>
    </div>

    <h6 class="mb-0 mt-3">{{ $client->first_name }} {{ $client->last_name }}</h6>
    
    <small class="text-muted">
        @if($client->dossiers->isNotEmpty())
            {{ $client->dossiers->first()->type }}
        @else
            Geen dossier
        @endif
    </small>
</div>
