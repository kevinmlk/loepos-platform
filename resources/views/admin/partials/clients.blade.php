<section class="flex flex-col gap-8">
    <div class="container mt-4">
        <h2 class="mb-4">Cliënten in mijn organisatie</h2>
        <table>
            <thead>
                <th class="text-start text-caption font-regular py-2">Naam</th>
                <th class="text-start text-caption font-regular py-2">Laatst bijgewerkt</th>
                <th class="text-start text-caption font-regular py-2">Aangemaakt op</th>
            </thead>
            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td class="text-blue">
                        <a href="/admin/clients/{{ $client->id }}" class="flex items-center gap-4 py-3">
                            <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                                <x-phosphor-file-bold class="h-4 text-dark-blue" />
                            </div>
                            <span class="text-black hover:text-blue">{{ $client->first_name . ' ' . $client->last_name }}</span>
                        </a>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($client->updated_at)->translatedFormat('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($client->created_at)->translatedFormat('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</section>
