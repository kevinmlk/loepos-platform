<x-layout>
    <header class="flex justify-between">
        <div>
            <h1 class="text-4xl font-bold">Organisaties</h1>
            <p class="mt-1 text-dark-gray">Beheer hier alle organisaties die zijn aangesloten bij Loepos.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/organisation/create" class="mb-4 w-max" type="secondary">
                Organisatie toevoegen
            </x-ui.button>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-xl p-6">

        <table class="w-full">
            <thead>
                <th class="text-start text-caption font-regular py-2">Naam</th>
                <th class="text-start text-caption font-regular py-2 px-6">GSM-nummer</th>
                <th class="text-start text-caption font-regular py-2 px-6">Email</th>
            </thead>
            <tbody>
                @foreach($organizations as $organization)
                    <x-shared.organisations-row
                    :organization="$organization"
                    :name="$organization->name"
                    :phone="$organization->phone"
                    :email="$organization->email"
                />
                @endforeach
            </tbody>
        </table>

        <div class="mt-2">
            {{ $organizations->links() }}
        </div>

    </section>

</x-layout>
