<x-layout>
    {{-- Header --}}
    <x-headerAdmin>
        Organisaties
        <x-slot:subText>
            Beheer hier alle organisaties die zijn aangesloten bij Loepos.
        </x-slot:subText>
    </x-headerAdmin>

    <section class="border-2 border-light-gray rounded-xl p-6">
    
        <table class="w-full">
            <thead>
                <th class="text-start text-caption font-regular py-2">Naam</th>
                <th class="text-start text-caption font-regular py-2 px-6">Status</th>
                <th class="text-start text-caption font-regular py-2 px-6">GSM-nummer</th>
                <th class="text-start text-caption font-regular py-2 px-6">Website</th>
            </thead>
            <tbody>
                @foreach($organizations as $organization)
                    <x-shared.organisations-row
                    :organization="$organization"
                    :name="$organization->name"
                    :status="$organization->status"
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
