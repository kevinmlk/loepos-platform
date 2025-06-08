<x-layout>
    <header class="flex justify-between">
        <div>
            <h1 class="text-4xl font-bold">{{ $organization->name }}</h1>
            <p class="mt-1 text-dark-gray">Bekijk hier alle info over de organisatie.</p>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-xl p-6">

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
        <x-ui.button href="{{ route('organisations.index') }}" class="inline-block mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg">
            ← Terug naar lijst
        </x-ui.button>
    </section>
</x-layout>
