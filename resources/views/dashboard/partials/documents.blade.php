<div class="flex justify-between gap-6">
    <article class="border-2 border-light-gray rounded-lg p-6 flex flex-col gap-4 w-full">
        <h2>Laatst toegevoegd</h2>
        <!-- Display uploaded documents -->
        <table>
            <thead>
                <th class="text-start text-caption font-regular py-2">Bestandsnaam</th>
                <th class="text-start text-caption font-regular py-2">Datum geüpload</th>
                <th class="text-start text-caption font-regular py-2">Laatst bewerkt</th>
            </thead>
            <tbody>
                @foreach($documents as $document)
                    <x-shared.document-row
                        :fileName="$document->file_name"
                        :filePath="$document->file_path"
                        :createdAt="$document->created_at"
                        :updatedAt="$document->updated_at"
                    />
                @endforeach
            </tbody>
        </table>
    </article>

    <article class="w-100 text-center border-2 border-light-gray rounded-lg p-6 flex flex-col justify-center gap-4">
        <h1 class="text-display font-semibold">{{ count($documents) }}</h1>
        <p class="text-caption">nieuwe document</p>
    </article>
</div>
