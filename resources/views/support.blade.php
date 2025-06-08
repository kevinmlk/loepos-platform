<x-layout>
    {{-- Header --}}
    <x-header>
        Ondersteuning
        <x-slot:subText>
            Contacteer onze IT afdeling met vragen.
        </x-slot:subText>
    </x-header>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Info tekst --}}
    <p>Gebruik onderstaand formulier om contact op te nemen met onze IT afdeling.</p>

    {{-- Formulier --}}
    <form method="POST" action="{{ route('support.send') }}" class="space-y-4 mt-4 max-w-lg">
        @csrf

        {{-- Organisatie (readonly) --}}
        <div>
            <label for="organization" class="block font-medium">Organisatie</label>
            <input type="text" id="organization" name="organization" 
                   value="{{ auth()->user()->organization->name ?? '' }}" 
                   readonly 
                   class="border rounded w-full p-2 bg-gray-100">
        </div>

        {{-- E-mailadres (readonly) --}}
        <div>
            <label for="email" class="block font-medium">E-mailadres</label>
            <input type="email" id="email" name="email" 
                   value="{{ auth()->user()->email }}" 
                   readonly 
                   class="border rounded w-full p-2 bg-gray-100">
        </div>

        {{-- Bericht --}}
        <div>
            <label for="message" class="block font-medium">Bericht</label>
            <textarea id="message" name="message" rows="5" required class="border rounded w-full p-2"></textarea>
        </div>

        <button type="submit" class="bg-blue text-white hover:bg-dark-blue bgtext-white px-4 py-2 rounded">Verzenden</button>
    </form>
</x-layout>
