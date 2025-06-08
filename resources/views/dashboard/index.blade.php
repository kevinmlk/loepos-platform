<x-layout>
    {{-- Header --}}
    <x-header>
        Welkom terug, {{ Auth()->user()->first_name }}!
        <x-slot:subText>
            Traceer en beheer je cliënten en hun dossiers.
        </x-slot:subText>
    </x-header>

    <section class="flex flex-col gap-6">
        @include('dashboard.partials.analytics')
        <div class="flex lg:flex-row flex-col justify-between gap-6">
            <article class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4 w-full">
                <div class="flex justify-between">
                    <h2>Laatst toegevoegd</h2>
                    <div class="flex gap-3">
                        <x-ui.button-small href="/dossiers" type="primary">Dossiers</x-ui.button-small>
                    </div>
                </div>
                <table>
                    <thead>
                        <th class="text-start text-caption font-regular py-2">Dossier</th>
                        <th class="text-start text-caption font-regular py-2">Nieuwe documenten</th>
                        <th class="text-start text-caption font-regular py-2">Laatst bijgewerkt</th>
                    </thead>
                    <tbody>
                        @foreach($dossiers as $dossier)
                            <tr>
                                <td class="text-blue py-4">
                                    <a href="/dossiers/{{ $dossier->id }}"  class="flex items-center gap-4">
                                        <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                                            {{ substr($dossier->client->first_name, 0, 1) . substr($dossier->client->last_name, 0, 1) }}
                                        </div>
                                        <span class="text-black hover:text-blue">{{ $dossier->client->first_name . " " . $dossier->client->last_name }}</span>
                                    </a>
                                </td>
                                <td>
                                    {{ count($dossier->documents) }}
                                </td>
                                <td>
                                    {{ $dossier->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </article>
            <!-- Aantal documenten in de uploads weergeven in de plaats van de uploads, knop naar de Wachtrij -->
            <article class="w-100 text-center border-2 border-light-gray rounded-lg p-6 flex flex-col justify-center gap-4">
                <h1 class="text-display font-semibold">{{ count($latestUploads) }}</h1>
                <p class="text-caption">Nieuwe uploads</p>
                <x-ui.button type="tertiary" class="justify-center" href="/uploads">Bekijk uploads</x-ui.button>
            </article>
        </div>
    </section>
</x-layout>
