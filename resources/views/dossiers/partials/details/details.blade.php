<section class="flex flex-col">
    <div class="grid-cols-2">
        <article class="border-2 rounded-xl border-light-gray p-6">
            <h2>Dossier gegevens</h2>
            <div class="flex">
                <div>
                    <span>Huidige status</span>
                    <p>{{ $dossier->status }}</p>
                </div>

                <div>
                    <span>Type hulp</span>
                    <p>Budgetbeheer</p>
                </div>

                <div>
                    <span>Toegewezen medewerker</span>
                    <p>{{ $dossier->user->first_name }} {{ $dossier->user->last_name }}</p>
                </div>
            </div>
        </article>

        <article class="border-2 rounded-xl border-light-gray p-6 flex gap-6 items-center">
            <div class="bg-transparant-blue rounded-full w-20 h-20 flex justify-center items-center text-body-large font-medium text-blue">
                {{ substr($dossier->client->first_name, 0, 1) . substr($dossier->client->last_name, 0, 1) }}
            </div>
            <h2>{{ $dossier->client->first_name }} {{ $dossier->client->last_name }}</h2>
        </article>
    </div>
</section>
