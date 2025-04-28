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
    <body class="font-inter text-black bg-white flex max-h-screen ">
        {{-- Navigation --}}
        <nav class="max-w-xs h-screen border-r-2 py-6 px-4  border-light-gray flex flex-col justify-between">
            <div class="flex flex-col items-start gap-6">
                <img src="{{ asset('images/Logo_LOEPOS_1.png') }}" alt="LoePos Logo" class="h-14">
                <ul>
                    {{-- Home nav link --}}
                    <x-nav-link-container>
                        <x-nav-link href="/" :active="request()->is('/')">
                            <x-phosphor-house-bold class="w-6 h-6 mr-3" />Home
                        </x-nav-link>
                    </x-nav-link-container>

                    {{-- Post processing nav link --}}
                    <x-nav-link-container>
                        <x-nav-link href="/dossiers" :active="request()->is('dossiers')"><x-phosphor-mailbox-bold class="w-6 h-6 mr-3" />Dossiers
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
            </div>

            <div>
                <x-nav-link href="/settings" :active="request()->is('settings')"><x-phosphor-gear-bold class="w-6 h-6 mr-3" />Instellingen
                </x-nav-link>
                {{-- Logout button --}}
                <form method="POST" action="/logout">
                    @csrf
                    <x-nav-link type="button"><x-phosphor-sign-out-bold class="w-6 h-6 mr-3" />Afmelden
                    </x-nav-link>
                </form>
            </div>
        </nav>

        {{-- Main --}}
        <main class="py-6 px-14 w-screen flex flex-col gap-8">
            {{ $slot }}
        </main>
    </body>
</html>
