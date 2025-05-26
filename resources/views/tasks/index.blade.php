<x-layout>
    {{-- Header --}}
    <x-header>
        Dossier inbox
        <x-slot:subText>
            Hier vind je een overzicht van alle dossiers.
        </x-slot:subText>
    </x-header>

    <div class="flex gap-4">
        <a
            href="/dossiers"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Overzicht
        </a>

        <a
            href="/tasks"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            Inbox
        </a>
    </div>

    {{-- Section to display the dossiers and inbox --}}
    <section class="border-2 border-light-gray rounded-xl p-6">
        <table>
            <thead>
                <th class="text-start text-caption font-regular py-2">Document</th>
                <th class="text-start text-caption font-regular py-2">Dossier</th>
                <th class="text-start text-caption font-regular py-2">Omschrijving</th>
                <th class="text-start text-caption font-regular py-2">Status</th>
                <th class="text-start text-caption font-regular py-2">Vervaldatum</th>
                <th class="text-start text-caption font-regular py-2">Urgentie</th>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr class="border-b border-gray">
                    <td>{{ $task->document->file_name }}</td>
                    <td>{{ $task->document->dossier->client->first_name }} {{ $task->document->dossier->client->last_name }}</td>
                    <td>{{ $task->description }}</td>
                    <td>{{ $task->status }}</td>
                    <td>{{ \Carbon\Carbon::parse($task->due_date)->translatedFormat('d M Y') }}</td>
                    <td>{{ $task->urgency }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </section>
</x-layout>
