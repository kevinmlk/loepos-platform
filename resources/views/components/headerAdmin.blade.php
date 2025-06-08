<header class="flex justify-between">
    <div>
        <h1 class="text-4xl font-bold">{{ $slot }}</h1>
        <p class="mt-1 text-dark-gray">{{ $subText }}</p>
    </div>
    <div class="flex gap-4">
        <x-ui.button href="/admin/employee/create" class="mb-4 w-max" type="secondary">
        Medewerker toevoegen
        </x-ui.button>
        <x-ui.button href="/admin/client/create" class="mb-4 w-max" type="secondary">
        Cliënt toevoegen
        </x-ui.button>
    </div>
</header>
