<div class="p-4 bg-white rounded shadow">
    <h3 class="text-xl font-semibold mb-2">{{ $user->name }}</h3>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Telefoon:</strong> {{ $user->phone ?? 'Niet beschikbaar' }}</p>
    <p><strong>Adres:</strong> {{ $user->address ?? 'Niet beschikbaar' }}</p>

    {{-- Add any other fields you want --}}
</div>
