<section class="flex flex-col gap-8">
    <div class="grid grid-cols-2 gap-8">
        <article class="border-2 rounded-xl border-light-gray p-6 flex flex-col gap-5">
            <h2>Dossier gegevens</h2>
            <div class="flex flex-wrap gap-x-48 gap-y-6">
                <div class="flex flex-col gap-1">
                    <span class="text-caption">Huidige status</span>
                    <p class="text-body-default">{{ $dossier->status }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Type hulp</span>
                    <p class="text-body-default">Budgetbeheer</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Toegewezen medewerker</span>
                    <p class="text-body-default">{{ $dossier->user->first_name }} {{ $dossier->user->last_name }}</p>
                </div>
            </div>
        </article>

        <article class="border-2 rounded-xl border-light-gray p-6 flex gap-6 items-center h-30">
            <div class="bg-transparant-blue rounded-full w-20 h-20 flex justify-center items-center text-header-2 font-medium text-blue">
                {{ substr($dossier->client->first_name, 0, 1) . substr($dossier->client->last_name, 0, 1) }}
            </div>
            <h2>{{ $dossier->client->first_name }} {{ $dossier->client->last_name }}</h2>
        </article>
    </div>

    <div>
        <article class="border-2 rounded-xl border-light-gray p-6 flex flex-col gap-5">
            <h2>Financiële informatie</h2>
            <div class="flex flex-wrap gap-x-48 gap-y-6">
                <div class="flex flex-col gap-1">
                    <span class="text-caption">Huidige status</span>
                    <p class="text-body-default">{{ $dossier->status }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Type hulp</span>
                    <p class="text-body-default">Budgetbeheer</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Toegewezen medewerker</span>
                    <p class="text-body-default">{{ $dossier->user->first_name }} {{ $dossier->user->last_name }}</p>
                </div>
            </div>
        </article>

        <article class="border-2 rounded-xl border-light-gray p-6 flex flex-col gap-5">
            <h2>Persoonlijke informatie</h2>
            <div class="flex flex-wrap gap-x-48 gap-y-6">
                <div class="flex flex-col gap-1">
                    <span class="text-caption">Voornaam</span>
                    <p class="text-body-default">{{ $dossier->client->first_name }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Achternaam</span>
                    <p class="text-body-default">{{ $dossier->client->last_name }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Adres</span>
                    <p class="text-body-default">{{ $dossier->client->address }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Geboortedatum en -plaats</span>
                    <p class="text-body-default">12 MEI 1988, Rabat, Marokko</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">GSM-nummer</span>
                    <p class="text-body-default">{{ $dossier->client->phone }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">E-mail</span>
                    <p class="text-body-default">{{ $dossier->client->email }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Rijksregister</span>
                    <p class="text-body-default">{{ $dossier->client->national_registry_number }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">ID-kaartnummer</span>
                    <p class="text-body-default">BE12 3456 7890 1234</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Nationaliteit</span>
                    <p class="text-body-default">Belgisch</p>
                </div>
            </div>
        </article>
    </div>
</section>
