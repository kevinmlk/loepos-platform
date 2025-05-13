<section class="flex flex-col gap-8">
    <div class="grid grid-cols-2 gap-8">
        <article class="border-2 rounded-xl border-light-gray p-6 flex flex-col gap-5">
            <h2>Dossier gegevens</h2>
            <div class="grid grid-cols-2 gap-8">
                <div class="flex flex-col gap-1">
                    <span class="text-caption">Huidige status</span>
                    <p class="text-body-default">{{ $dossier->status }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Type hulp</span>
                    <p class="text-body-default">{{ $dossier->type }}</p>
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

    <div class="grid grid-cols-2 gap-8">
        <article class="border-2 rounded-xl border-light-gray p-6 flex flex-col gap-5 h-55">
            <h2>Financiële informatie</h2>
            <div class="grid grid-cols-2 gap-8">
                <div class="flex flex-col gap-1">
                    <span class="text-caption">Schulden</span>
                    <p class="text-body-default">
                        € {{ $dossier->debts->sum('amount') }}
                    </p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Uitgaven</span>
                    @if (!empty($dossier->client->financialInfo->monthly_expenses))
                    <p class="text-body-default">€ {{ $dossier->client->financialInfo->monthly_expenses }}</p>
                    @else
                    <p class="text-body-default">Geen uitgaven</p>
                    @endif
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Inkomen</span>
                    <p class="text-body-default">€ {{ $dossier->client->financialInfo->monthly_income }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Budgetbeheer</span>
                    <p class="text-body-default">Sinds {{ $dossier->client->created_at->format('F Y') }}</p>
                </div>
            </div>
        </article>

        <article class="border-2 rounded-xl border-light-gray p-6 flex flex-col gap-5">
            <h2>Persoonlijke informatie</h2>
            <div class="grid grid-cols-2 gap-8">
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
                    <p class="text-body-default">12 MEI 1988, Mechelen, België</p>
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
                    <p class="text-body-default">{{ $dossier->client->financialInfo->iban }}</p>
                </div>

                <div class="flex flex-col gap-1">
                    <span class="text-caption">Nationaliteit</span>
                    <p class="text-body-default">Belgisch</p>
                </div>
            </div>
        </article>
    </div>
</section>
