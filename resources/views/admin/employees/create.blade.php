<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Medewerker toevoegen</h1>
            <p class="mt-1 text-dark-gray">Maak een nieuwe medewerker aan.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/admin" class="mb-4 w-max" type="secondary">
                Terug naar overzicht
            </x-ui.button>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
        <h2>Medewerker toevoegen</h2>

        {{-- Upload document form --}}
        <form action="/admin/employee"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="employee-form">
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
                <x-form.label for="password">Wachtwoord</x-form.label>
                <x-form.input type="password" id="password" name="password" required />
                @error('password')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="password_confirmation">Herhaal wachtwoord</x-form.label>
                <x-form.input type="password" id="password_confirmation" name="password_confirmation" required />
                @error('password')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-ui.button type="primary">Toevoegen</x-ui.button>
            </div>
        </form>

    </section>

</x-layout>
