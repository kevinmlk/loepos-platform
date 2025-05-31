<x-layout>
    <x-headerAdmin>
        Organisatie bewerken: {{ $organization->name }}
        <x-slot:subText>
            Pas hier de gegevens van de organisatie aan.
        </x-slot:subText>
    </x-headerAdmin>

    <section class="border-2 border-light-gray rounded-xl p-6 max-h-[calc(100vh-150px)] overflow-y-auto">
        <form method="POST" action="{{ route('organisations.update', $organization) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block font-semibold mb-1">Naam</label>
                <input type="text" name="name" id="name" value="{{ old('name', $organization->name) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="status" class="block font-semibold mb-1">Status</label>
                <input type="text" name="status" id="status" value="{{ old('status', $organization->status) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="phone" class="block font-semibold mb-1">GSM-nummer</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $organization->phone) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="email" class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $organization->email) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="website" class="block font-semibold mb-1">Website</label>
                <input type="text" name="website" id="website" value="{{ old('website', $organization->website) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="VAT" class="block font-semibold mb-1">VAT</label>
                <input type="text" name="VAT" id="VAT" value="{{ old('VAT', $organization->VAT) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="address" class="block font-semibold mb-1">Adres</label>
                <input type="text" name="address" id="address" value="{{ old('address', $organization->address) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="postal_code" class="block font-semibold mb-1">Postcode</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $organization->postal_code) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="city" class="block font-semibold mb-1">Stad</label>
                <input type="text" name="city" id="city" value="{{ old('city', $organization->city) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="mb-4">
                <label for="country" class="block font-semibold mb-1">Land</label>
                <input type="text" name="country" id="country" value="{{ old('country', $organization->country) }}" class="border border-gray-300 rounded-lg p-2 w-full">
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg">Opslaan</button>
                <a href="{{ route('organisations.show', $organization) }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Annuleren</a>
            </div>
        </form>
    </section>
</x-layout>
