<div class="card text-center p-3 shadow-sm rounded" style="border: none;">
    <div class="d-flex justify-content-between align-items-start">
        <i class="bi bi-person-badge" style="font-size: 1.2rem;"></i>
        <div x-data="{ open: false }" class="relative text-right">
            <button @click="open = !open" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <i class="bi bi-three-dots-vertical"></i>
            </button>

            <div x-show="open"
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded shadow-lg z-50"
            >
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Bewerken</a>
                <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Verwijderen</a>
            </div>
        </div>
    </div>

    <!-- <img src="https://via.placeholder.com/64" class="rounded-circle mx-auto d-block mt-2 mb-2" alt="Profile Picture" width="64" height="64"> -->
    
    <h6 class="mb-0">{{ $user->first_name }} {{ $user->last_name }}</h6>
    <small class="text-muted">
        {{ $user->role === 'employee' ? 'Employee' : ucfirst($user->role) }}
    </small>
</div>
