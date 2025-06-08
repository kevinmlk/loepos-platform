<header class="flex flex-col md:flex-row justify-between gap-4">
    <div>
        <h1 class="text-4xl font-bold">{{ $slot }}</h1>
        <p class="mt-1 text-dark-gray">{{ $subText }}</p>
    </div>

    <div class="flex gap-3">
        <x-ui.button href="/upload" type="secondary" icon="phosphor-upload-simple-bold">Uploaden</x-ui.button>
        @if (!request()->is('queue') && !request()->is('queue/verify'))
            <x-ui.button href="/queue" type="primary">Wachtrij</x-ui.button>
        @endif
    </div>
</header>
