<div class="flex justify-between">
    <article class="border-2 border-light-gray rounded-lg p-6 flex justify-between gap-12">
        <div>
            <span class="text-header-2 font-semibold">Te betalen facturen</span>

        </div>

        <div class="flex flex-col gap-3">
            <span class="text-header-2 font-semibold">Opeenstaande schuld</span>
            <div class="w-full bg-light-gray rounded-full h-2">
                @php
                    $totalDebt = $dossier->debts->sum('amount');
                    $totalPaid = $dossier->debts->sum('amount_paid');
                    $progress = $totalDebt > 0 ? ($totalPaid / $totalDebt) * 100 : 0;
                @endphp
                <div class="bg-yellow h-2 rounded-full" style="width: {{ $progress }}%;"></div>
            </div>
            <p>Te betalen bedrag: € {{ $totalDebt - $totalPaid }}</p>
        </div>
    </article>

    <article class="border-2 border-light-gray rounded-lg p-6">
        <h2>Todo</h2>
    </article>
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
                    <td>{{ $document->file_name }}</td>
                    <td>{{ $document->type }}</td>
                    <td>{{ $document->created_at }}</td>
                    <td>{{ $document->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</article>
