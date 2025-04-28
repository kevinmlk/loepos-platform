<header class="flex justify-between">
    <div>
        <h1 class="text-4xl font-bold">{{ $slot }}</h1>
        <p class="mt-1 text-dark-gray">Traceer en beheer je cliënten en hun dossiers.</p>
    </div>

    <div class="flex  gap-3">
        <x-ui.button href="/documents" type="secondary" icon="phosphor-upload-simple-bold">Uploaden</x-ui.button>
        <x-ui.button href="post-processing" type="primary">AI queue</x-ui.button>
    </div>
</header>
