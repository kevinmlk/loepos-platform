<header class="flex justify-between">
    <div>
        <h1 class="text-4xl font-bold">{{ $slot }}</h1>
        <p class="mt-1 text-dark-gray">{{ $subText }}</p>
    </div>

    <div class="flex  gap-3">
        <x-ui.button href="/documents?tab=upload" type="secondary" icon="phosphor-upload-simple-bold">Uploaden</x-ui.button>
        <x-ui.button href="/queue" type="primary">AI queue</x-ui.button>
    </div>
</header>
