<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Organisatie toevoegen</h1>
            <p class="mt-1 text-dark-gray">Maak een nieuwe organisatie aan.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/organisations" class="mb-4 w-max" type="secondary">
                Terug naar overzicht
            </x-ui.button>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4 overflow-y-auto">
        <h2>Maak een nieuwe organisatie aan</h2>

        {{-- Upload document form --}}
        <form action="/organisation"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="organisation-form">
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
                <x-form.label for="name">Naam</x-form.label>
                <x-form.input type="text" id="name" name="name" value="{{ old('name') }}" required />
                @error('name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="email">Email</x-form.label>
                <x-form.input type="email" id="email" name="email" value="{{ old('email') }}" required />
                @error('email')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="phone">Phone</x-form.label>
                <x-form.input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required />
                @error('phone')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="website">Website</x-form.label>
                <x-form.input type="url" id="website" name="website" value="{{ old('website') }}" required />
                @error('website')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="VAT">VAT</x-form.label>
                <x-form.input type="text" id="VAT" name="VAT" value="{{ old('VAT') }}" required />
                @error('VAT')
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
                <x-form.label for="postal_code">Postcode</x-form.label>
                <x-form.input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required />
                @error('postal_code')
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
                <x-form.label for="country">Country</x-form.label>
                <x-form.input type="text" id="country" name="country" value="{{ old('country') }}" required />
                @error('country')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-ui.button type="primary">Toevoegen</x-ui.button>
            </div>
        </form>
    </section>
</x-layout>
