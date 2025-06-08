
<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Gebruiker toevoegen</h1>
            <p class="mt-1 text-dark-gray">Vul de gegevens in voor de nieuwe gebruiker.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/users" class="mb-4 w-max" type="secondary">
                Terug naar overzicht
            </x-ui.button>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
        <h2>Gebruikersgegevens</h2>

        <form action="/user"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="user-form">
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
                <x-form.input type="text" id="first_name" name="first_name"  required />
                @error('first_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="last_name">Achternaam</x-form.label>
                <x-form.input type="text" id="last_name" name="last_name"  required />
                @error('last_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="email">E-mailadres</x-form.label>
                <x-form.input type="email" id="email" name="email" required />
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
                <x-form.label for="organization">Organisatie (selecteer)</x-form.label>
                <select id="organization" name="organization"
                    class="form-select w-full border-2 border-light-gray rounded-lg px-4 py-2 focus:outline-none focus:border-blue transition"
                    required>
                    <option value="">Selecteer een organisatie</option>
                    @foreach ($organizations as $organization)
                        <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                    @endforeach
                </select>
                @error('organization')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-form.label for="role_select">Rol (selecteer)</x-form.label>
                <select id="role_select" name="role_select"
                    class="form-select w-full border-2 border-light-gray rounded-lg px-4 py-2 focus:outline-none focus:border-blue transition"
                    required>
                    <option value="">Selecteer een rol</option>
                    @foreach ($roles as $role)
                        @if ($role !== 'superadmin')
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endif
                    @endforeach
                </select>
                @error('role_select')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-ui.button type="primary">Toevoegen</x-ui.button>
            </div>
        </form>

    </section>

</x-layout>
