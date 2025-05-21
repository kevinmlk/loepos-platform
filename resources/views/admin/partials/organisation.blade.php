<section class="flex flex-col gap-8" x-data="{ editing: false }">
    <h3 class="text-xl font-semibold">Organisatiegegevens</h3>

    {{-- Display View --}}
    <div x-show="!editing" class="flex flex-wrap">
        <div class="w-full md:w-1/2 lg:w-1/3 p-4">
            <div><strong>Naam organisatie:</strong></div>
            <div>{{ $organization->name }}</div><br>

            <div><strong>Telefoon:</strong></div>
            <div>{{ $organization->phone }}</div><br>

            <div><strong>Website:</strong></div>
            <div>{{ $organization->website }}</div><br>
        </div>

        <div class="w-full md:w-1/2 lg:w-1/3 p-4">
            <div><strong>Adres:</strong></div>
            <div>{{ $organization->address }}, {{ $organization->postal_code }} {{ $organization->city }}</div><br>

            <div><strong>E-mail:</strong></div>
            <div>{{ $organization->email }}</div><br>

            <div><strong>Ondernemingsnummer (VAT):</strong></div>
            <div>{{ $organization->VAT }}</div><br>
        </div>

        <div class="w-full p-4">
            <x-ui.button type="primary" @click="editing = true">Bewerken</x-ui.button>
        </div>
    </div>

    {{-- Edit Form --}}
    <form x-show="editing" x-cloak @submit.prevent="submitForm" class="bg-gray-50 p-4 rounded shadow">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm mb-1">Naam organisatie</label>
                <input type="text" name="name" id="name" value="{{ $organization->name }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="phone" class="block text-sm mb-1">Telefoon</label>
                <input type="text" name="phone" id="phone" value="{{ $organization->phone }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="website" class="block text-sm mb-1">Website</label>
                <input type="text" name="website" id="website" value="{{ $organization->website }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="email" class="block text-sm mb-1">E-mail</label>
                <input type="email" name="email" id="email" value="{{ $organization->email }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="address" class="block text-sm mb-1">Adres</label>
                <input type="text" name="address" id="address" value="{{ $organization->address }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="postal_code" class="block text-sm mb-1">Postcode</label>
                <input type="text" name="postal_code" id="postal_code" value="{{ $organization->postal_code }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="city" class="block text-sm mb-1">Stad</label>
                <input type="text" name="city" id="city" value="{{ $organization->city }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="country" class="block text-sm mb-1">Land</label>
                <input type="text" name="country" id="country" value="{{ $organization->country }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>

            <div>
                <label for="VAT" class="block text-sm mb-1">Ondernemingsnummer (VAT)</label>
                <input type="text" name="VAT" id="VAT" value="{{ $organization->VAT }}"
                    class="form-input w-full border border-gray-300 rounded px-3 py-2" />
            </div>
        </div>

        <div class="mt-4 flex gap-2">
            <x-ui.button type="primary">Opslaan</x-ui.button>
            <x-ui.button type="secondary" @click="editing = false">Annuleren</x-ui.button>
        </div>
    </form>

    {{-- Alpine Handler --}}
    <script>
        function submitForm() {
            const form = event.target.closest('form');
            const formData = new FormData(form);

            fetch('/admin/organisation/update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData,
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Opslaan mislukt.');
                }
            })
            .catch(() => alert('Fout bij verzenden.'));
        }
    </script>
</section>
