<x-layout>
    <header class="flex flex-col md:flex-row justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold">Gebruikers overzicht</h1>
            <p class="mt-1 text-dark-gray">Beheer hier al uw gebruikers.</p>
        </div>
        <div class="flex gap-4">
            <x-ui.button href="/user/create" class="mb-4 w-max" type="primary">
                Gebruiker toevoegen
            </x-ui.button>
        </div>
    </header>
    <section class="flex flex-col gap-8">
        <div class="container mt-4 border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
            <h2 class="mb-4">Cliënten in mijn organisatie</h2>
            <table>
                <thead>
                    <th class="text-start text-caption font-regular py-2">Naam</th>
                    <th class="text-start text-caption font-regular py-2">Organisatie</th>
                    <th class="text-start text-caption font-regular py-2">Laatst bijgewerkt</th>
                    <th class="text-start text-caption font-regular py-2">Aangemaakt op</th>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-b border-light-gray hover:cursor-pointer transition duration-150">
                        <td class="text-blue">
                            <a href="/user/{{ $user->id }}" class="flex items-center gap-4 py-3">
                                <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                                    <x-phosphor-file-bold class="h-4 text-dark-blue" />
                                </div>
                                <span class="text-black hover:text-blue">{{ $user->first_name . ' ' . $user->last_name }}</span>
                            </a>
                        </td>
                        @if($user->organization)
                            <td>
                                {{ $user->organization->name }}
                            </td>
                        @else
                            <td>
                                <span class="text-gray-500">Geen organisatie</span>
                            </td>
                        @endif
                        <td>{{ \Carbon\Carbon::parse($user->updated_at)->translatedFormat('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-2">
                {{ $users->links() }}
            </div>
        </div>
    </section>
</x-layout>
