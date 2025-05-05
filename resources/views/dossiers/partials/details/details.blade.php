<p><strong>Client Name:</strong> {{ $dossier->client->first_name }} {{ $dossier->client->last_name }}</p>
<p><strong>Status:</strong> {{ $dossier->status }}</p>
<p><strong>Phone:</strong> {{ $dossier->client->phone }}</p>
<p><strong>Email:</strong> {{ $dossier->client->email }}</p>
