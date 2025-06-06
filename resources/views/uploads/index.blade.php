
<x-layout>
    <x-header>
        AI queue
        <x-slot:subText>
            Beheer de uploads hier.
        </x-slot:subText>
    </x-header>

    <div class="flex gap-4">
        <a
            href="/documents"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Overzicht
        </a>

        <a
            href="/upload/create"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium"
        >
            Upload
        </a>

        <a
            href="/uploads"
            class="px-4 py-2 rounded-md capitalize transition-colors duration-100 text-button font-medium bg-blue text-white"
        >
            AI queue
        </a>
    </div>

    <section class="flex flex-col gap-8">
        <article>
            <div class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
                <div class="flex justify-between">
                    <h2>Uploaded files</h2>
                </div>
                <table>
                    <thead>
                        <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
                        <th class="text-start text-caption font-regular py-2">Documenten</th>
                        <th class="text-start text-caption font-regular py-2">Status</th>
                        <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
                        <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
                    </thead>
                    <tbody>
                        @foreach($uploads as $upload)
                        <tr>
                            <td class="text-blue">
                                <a href="/uploads/{{ $upload->id }}" class="flex items-center gap-4 py-3">
                                    <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                                        <x-phosphor-file-bold class="h-4 text-dark-blue" />
                                    </div>
                                    <span class="text-black hover:text-blue">{{ $upload->file_name }}</span>
                                </a>
                            </td>
                            <td>{{ $upload->documents  }}</td>
                            <td>{{ $upload->status }}</td>
                            <td>{{ \Carbon\Carbon::parse($upload->updated_at)->translatedFormat('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($upload->created_at)->translatedFormat('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>

        <article>
            <div class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
                <div class="flex justify-between">
                    <h2>Niet toegewezen uploads</h2>
                </div>
                <table>
                    <thead>
                        <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
                        <th class="text-start text-caption font-regular py-2">Documenten</th>
                        <th class="text-start text-caption font-regular py-2">Status</th>
                        <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
                        <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
                    </thead>
                    <tbody>
                        @foreach($unassignedUploads as $upload)
                        <tr>
                            <td class="text-blue">
                                <a href="/uploads/{{ $upload->id }}" class="flex items-center gap-4 py-3">
                                    <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                                        <x-phosphor-file-bold class="h-4 text-dark-blue" />
                                    </div>
                                    <span class="text-black hover:text-blue">{{ $upload->file_name }}</span>
                                </a>
                            </td>
                            <td>{{ $upload->documents  }}</td>
                            <td>{{ $upload->status }}</td>
                            <td>{{ \Carbon\Carbon::parse($upload->updated_at)->translatedFormat('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($upload->created_at)->translatedFormat('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                  </table>
            </div>
    </section>
</x-layout>
