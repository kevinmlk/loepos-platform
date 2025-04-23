<x-layout>
    {{-- Header --}}
    <x-header>Welkom terug, Sophie!</x-header>

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
