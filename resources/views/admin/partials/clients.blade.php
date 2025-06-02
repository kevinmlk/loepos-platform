<section class="flex flex-col gap-8">
    <div class="container mt-4">
        <h2 class="mb-4">Cliënten in mijn organisatie</h2>

        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-start text-caption font-regular py-2">Naam</th>
                    <th class="text-start text-caption font-regular py-2 px-6">GSM-nummer</th>
                    <th class="text-start text-caption font-regular py-2 px-6">E-mail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $client)
                    <x-shared.clients-row
                        :client="$client"
                        :first_name="$client->first_name"
                        :last_name="$client->last_name"
                        :status="$client->status"
                        :phone="$client->phone"
                        :email="$client->email"
                    />
                @endforeach


                

            </tbody>
        </table>

    </div>
</section>
