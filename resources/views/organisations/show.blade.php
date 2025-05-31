<x-layout>
    <x-headerAdmin>
        Organisatie: {{ $organization->name }}
        <x-slot:subText>
            Details van de geselecteerde organisatie.
        </x-slot:subText>
    </x-headerAdmin>

    <section class="border-2 border-light-gray rounded-xl p-6">

        <!-- Buttons in top-right inside the section -->
        <div class="absolute top-6 right-6 flex gap-4">
            <!-- Bewerken button -->
            <a href="{{ route('organisations.edit', $organization) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg">
                Bewerken
            </a>

            <!-- Verwijderen button -->
            <form action="{{ route('organisations.destroy', $organization) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze organisatie wilt verwijderen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg">
                    Verwijderen
                </button>
            </form>
        </div>

        <!-- Organization Details -->
        <div class="mb-4">
            <strong>Naam:</strong> {{ $organization->name }}
        </div>
        <div class="mb-4">
            <strong>Status:</strong> {{ $organization->status }}
        </div>
        <div class="mb-4">
            <strong>GSM-nummer:</strong> {{ $organization->phone }}
        </div>
        <div class="mb-4">
            <strong>Email:</strong> {{ $organization->email }}
        </div>
        <div class="mb-4">
            <strong>Website:</strong> {{ $organization->website }}
        </div>
        <div class="mb-4">
            <strong>VAT:</strong> {{ $organization->VAT }}
        </div>
        <div class="mb-4">
            <strong>Adres:</strong> {{ $organization->full_address }}
        </div>
        
        <!-- Back to list button -->
        <a href="{{ route('organisations.index') }}" class="inline-block mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg">
            ← Terug naar lijst
        </a>
    </section>
</x-layout>
