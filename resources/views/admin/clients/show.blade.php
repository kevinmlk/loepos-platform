<x-layout>
    {{-- Header --}}
    <x-header>
        Cliënt bewerken
        <x-slot:subText>
            Bewerk de gegevens van een bestaande cliënt.
        </x-slot:subText>
    </x-header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4 overflow-y-auto">
        <h2>Cliënt gegevens</h2>

        {{-- Upload document form --}}
        <form action="/admin/client/{{ $client->id }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="client-form">
            @csrf
            @method('PATCH')

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <x-form.label for="first_name">Voornaam</x-form.label>
                <x-form.input type="text" id="first_name" name="first_name" value="{{ $client->first_name }}" required />
                @error('first_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="last_name">Achternaam</x-form.label>
                <x-form.input type="text" id="last_name" name="last_name" value="{{ $client->last_name }}" required />
                @error('last_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="email">E-mailadres</x-form.label>
                <x-form.input type="email" id="email" name="email" value="{{ $client->email }}" required />
                @error('email')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="phone">Telefoon</x-form.label>
                <x-form.input type="tel" id="phone" name="phone" value="{{ $client->phone }}" required />
                @error('phone')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="address">Adres</x-form.label>
                <x-form.input type="text" id="address" name="address" value="{{ $client->address }}" required />
                @error('address')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="city">Stad</x-form.label>
                <x-form.input type="text" id="city" name="city" value="{{ $client->city }}" required />
                @error('city')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="postal_code">Postcode</x-form.label>
                <x-form.input type="texte" id="postal_code" name="postal_code" value="{{ $client->postal_code }}" required />
                @error('postal_code')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="country">Country</x-form.label>
                <x-form.input type="text" id="country" name="country" value="{{ $client->country}}" required />
                @error('country')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="national_registry_number">Rijksregisternummer</x-form.label>
                <x-form.input type="text" id="national_registry_number" name="national_registry_number" value="{{ $client->national_registry_number }}" required />
                @error('national_registry_number')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-ui.button type="primary">Bewerken</x-ui.button>
            </div>
        </form>
    </section>
</x-layout>
