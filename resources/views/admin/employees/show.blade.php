<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Medewerker bewerken</h1>
            <p class="mt-1 text-dark-gray">Gegevens van een medewerker bewerken.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/admin" class="mb-4 w-max" type="secondary">
                Terug naar overzicht
            </x-ui.button>
        </div>
    </header>

    <section class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
        <h2>Medewerker bewerken</h2>

        {{-- Upload document form --}}
        <form action="/admin/employee/{{ $employee->id }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6 mt-4"
            id="employee-form">
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
                <x-form.input type="text" id="first_name" name="first_name" value="{{ $employee->first_name }}" required />
                @error('first_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="last_name">Achternaam</x-form.label>
                <x-form.input type="text" id="last_name" name="last_name" value="{{ $employee->last_name }}" required />
                @error('last_name')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>

            <div>
                <x-form.label for="email">E-mailadres</x-form.label>
                <x-form.input type="email" id="email" name="email" value="{{$employee->email }}" required />
                @error('email')
                    <div class="text-red-500">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <x-ui.button type="primary">Bewerken</x-ui.button>
            </div>
        </form>

    </section>

</x-layout>
