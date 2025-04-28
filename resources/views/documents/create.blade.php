<x-layout>
    {{-- Header --}}
    <x-header>
        Document uploaden
        <x-slot:subText>
            Beheer de documenten van uw cliënten hier.
        </x-slot:subText>
    </x-header>

    <section class="mt-10">
        <h2>Document toevoegen</h2>

        {{-- Upload document form --}}
        <form action="/document" method="POST" enctype="multipart/form-data">
            @csrf
            <label for="document">Upload PDF or Image:</label>
            <input type="file" name="file" accept=".pdf,image/*" required>
            <x-ui.button type="primary">Upload</x-ui.button>
        </form>

    </section>

</x-layout>
