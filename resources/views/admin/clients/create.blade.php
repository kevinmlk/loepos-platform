<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Cliënt toevoegen</h1>
            <p class="mt-1 text-dark-gray">Maak een nieuwe cliënt aan.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/admin" class="mb-4 w-max" type="secondary">
                Terug naar overzicht
            </x-ui.button>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4 overflow-y-auto">
        <h2>Maak een nieuwe clïent aan</h2>

        {{-- Upload document form --}}
        <form action="/admin/client"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="client-form">
            @csrf

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
                <x-form.input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required />
                @error('first_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="last_name">Achternaam</x-form.label>
                <x-form.input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required />
                @error('last_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="email">E-mailadres</x-form.label>
                <x-form.input type="email" id="email" name="email" value="{{ old('email') }}" required />
                @error('email')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="phone">Telefoon</x-form.label>
                <x-form.input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required />
                @error('phone')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="address">Adres</x-form.label>
                <x-form.input type="text" id="address" name="address" value="{{ old('address') }}" required />
                @error('address')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="city">Stad</x-form.label>
                <x-form.input type="text" id="city" name="city" value="{{ old('city') }}" required />
                @error('city')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="postal_code">Postcode</x-form.label>
                <x-form.input type="texte" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required />
                @error('postal_code')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="country">Country</x-form.label>
                <x-form.input type="text" id="country" name="country" value="{{ old('country') }}" required />
                @error('country')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="national_registry_number">Rijksregisternummer</x-form.label>
                <x-form.input type="text" id="national_registry_number" name="national_registry_number" value="{{ old('national_registry_number') }}" required />
                @error('national_registry_number')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-ui.button type="primary">Toevoegen</x-ui.button>
            </div>
        </form>
    </section>
</x-layout>
