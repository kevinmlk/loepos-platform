
<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Gebruiker bewerken</h1>
            <p class="mt-1 text-dark-gray">Gegevens van de gebruiker bewerken.</p>
        </div>
        @if(auth()->user()->role === \App\Models\User::ROLE_SUPERADMIN)
        <div class="flex gap-4">
            <x-ui.button href="/users" class="mb-4 w-max" type="secondary">
                Terug naar overzicht
            </x-ui.button>
        </div>
        @endif
    </header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
        <h2>Gebruikersgegevens</h2>

        <form action="/user/{{ $user->id }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="user-form">
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
                <x-form.input type="text" id="first_name" name="first_name" value="{{ $user->first_name }}"   />
                @error('first_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="last_name">Achternaam</x-form.label>
                <x-form.input type="text" id="last_name" name="last_name" value="{{ $user->last_name }}"   />
                @error('last_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="email">E-mailadres</x-form.label>
                <x-form.input type="email" id="email" name="email" value="{{ $user->email }}"  />
                @error('email')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="password">Wachtwoord</x-form.label>
                <x-form.input type="password" id="password" name="password"  />
                @error('password')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="password_confirmation">Wachtwoord herhalen</x-form.label>
                <x-form.input type="password" id="password_confirmation" name="password_confirmation"  />
                @error('password_confirmation')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            @if(auth()->user()->role === \App\Models\User::ROLE_SUPERADMIN)
            <div>
                <x-form.label for="organization">Organisatie (selecteer)</x-form.label>
                <select id="organization" name="organization"
                    class="form-select w-full border-2 border-light-gray rounded-lg px-4 py-2 focus:outline-none focus:border-blue transition"
                    >
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
                    >
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
            @endif
            <div>
                <x-ui.button type="primary">Bewerken</x-ui.button>
            </div>
        </form>

    </section>

</x-layout>
