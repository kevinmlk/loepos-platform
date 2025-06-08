<div class="flex justify-between">
    <article class="border-2 border-light-gray rounded-lg p-6 w-full">
        <!-- <div>
            <span class="text-header-2 font-semibold">Te betalen facturen</span>

        </div> -->

        <div class="flex flex-col gap-3">
            <span class="text-header-2 font-semibold">Opeenstaande schuld</span>
            <div class="w-full bg-light-gray rounded-full h-2">
                @php
                    $totalDebt = $dossier->debts->sum('amount');
                    $totalPayments = $dossier->debts->flatMap->payments->sum('amount');
                    $progress = $totalDebt > 0 ? ($totalPayments / $totalDebt) * 100 : 0;
                @endphp
                <div class="bg-yellow h-2 rounded-full" style="width: {{ $progress }}%;"></div>
            </div>
            <p>Te betalen bedrag: € {{ $totalDebt - $totalPayments }}</p>
        </div>
    </article>

    <!-- <article class="border-2 border-light-gray rounded-lg p-6">
        <h2>Todo</h2>
    </article> -->
</div>
<article class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4">
    <h2>Facturen</h2>
    <!-- Display uploaded documents -->
    <table class="w-full">
        <thead>
            <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
            <th class="text-start text-caption font-regular py-2">Type</th>
            <th class="text-start text-caption font-regular py-2">Datum aankomst</th>
            <th class="text-start text-caption font-regular py-2">Einddatum</th>
        </thead>
        <tbody>
            @foreach($dossier->documents as $document)
                <tr>
                    <td class="text-blue">
                        <a href="{{ route('documents.view', $document->id) }}" target="_blank" class="flex items-center gap-4 py-3">
                            <div class="bg-transparant-blue rounded-full w-10 h-10 flex justify-center items-center">
                                <x-phosphor-file-bold class="h-4 text-dark-blue" />
                            </div>
                            <span class="text-black hover:text-blue">{{ $document->file_name }}</span>
                        </a>
                    </td>
                    <td>{{ $document->type }}</td>
                    <td>{{ \Carbon\Carbon::parse($document->created_at)->translatedFormat('d M Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($document->updated_at)->translatedFormat('d M Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</article>
