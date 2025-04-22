<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Loepos: Slimme oplossingen, Heldere toekomst</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-inter text-black bg-white flex max-h-screen">
        {{-- Navigation --}}
        <nav class="max-w-xs p-4 border-r-2 border-light-gray">
            <ul>
                {{-- Home nav link --}}
                <x-nav-link-container>
                    <x-nav-link href="/" :active="request()->is('/')">
                        <x-phosphor-house-bold class="w-6 h-6 mr-3" />Home
                    </x-nav-link>
                </x-nav-link-container>

                {{-- Post processing nav link --}}
                <x-nav-link-container>
                    <x-nav-link href="/post-processing" :active="request()->is('post-processing')"><x-phosphor-mailbox-bold class="w-6 h-6 mr-3" />Postverwerking
                    </x-nav-link>
                </x-nav-link-container>

                {{-- Documents nav link --}}
                <x-nav-link-container>
                    <x-nav-link href="/documents" :active="request()->is('documents')"><x-phosphor-folder-bold class="w-6 h-6 mr-3" />Documenten
                    </x-nav-link>
                </x-nav-link-container>

                {{-- Reports nav link --}}
                <x-nav-link-container>
                    <x-nav-link href="/reports" :active="request()->is('reports')"><x-phosphor-chart-bar-bold class="w-6 h-6 mr-3" />Rapporten
                    </x-nav-link>
                </x-nav-link-container>

                {{-- Support nav link --}}
                <x-nav-link-container>
                    <x-nav-link href="/support" :active="request()->is('support')"><x-phosphor-question-bold class="w-6 h-6 mr-3" />Ondersteuning
                    </x-nav-link>
                </x-nav-link-container>
            </ul>

            <ul>
                {{-- Logout button --}}
                <form method="POST" action="/logout">
                  @csrf
                  <x-ui.button>Afmelden</x-form.button>
                </form>
            </ul>
        </nav>

        {{-- Main --}}
        <main class="ml-14">
            {{ $slot }}
        </main>
    </body>
</html>
