<!DOCTYPE html>
<html lang="nl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description"
            content="Loepos biedt AI-gestuurde software voor schuldbemiddeling en cliëntbeheer. Vereenvoudig uw processen en verbeter de efficiëntie van uw sociale dienst.">
        <meta name="keywords" content="Loepos, Schuldbemiddeling, Cliëntbeheer, AI-gestuurde software, Sociale dienst, Efficiëntie, Procesoptimalisatie, Dossierbeheer, Documentbeheer, Rapportage, Ondersteuning, Organisatiebeheer">
        <meta name="author" content="Loepos">
        <title>Loepos: Slimme oplossingen, Heldere toekomst</title>

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

        {{-- Alpine.js --}}
        <script src="//unpkg.com/alpinejs" defer></script>

        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        {{-- Additional Styles --}}
        @stack('styles')

        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-5SMXPL9X');</script>
        <!-- End Google Tag Manager -->

    </head>
    <body class="font-inter text-black bg-white flex h-screen overflow-hidden">

        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5SMXPL9X"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        {{-- Navigation --}}
        <nav class="hidden lg:flex max-w-xs h-screen border-r-2 py-6 px-4 border-light-gray flex-col justify-between flex-shrink-0">            <div class="flex flex-col items-start gap-6">
                <picture>
                    <source srcset="{{ asset('images/Logo_LOEPOS_1.webp') }}" type="image/webp">
                    <source srcset="{{ asset('images/Logo_LOEPOS_1.png') }}" type="image/png">
                    <img src="{{ asset('images/Logo_LOEPOS_1.png') }}" alt="Loepos logo" class="h-14">
                </picture>
                <ul class="flex flex-col gap-2">
                    {{-- Home nav link --}}
                    @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_EMPLOYEE, \App\Models\User::ROLE_ADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/" :active="request()->is('/')">
                                        <x-phosphor-house-bold class="w-6 h-6 mr-3" /><span>Home</span>
                                    </x-nav-link>
                                </x-nav-link-container>
                            @endif
                    @endauth

                    {{-- Post processing nav link --}}
                    @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_EMPLOYEE, \App\Models\User::ROLE_ADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/dossiers" :active="request()->is('dossiers')">
                                        <x-phosphor-mailbox-bold class="w-6 h-6 mr-3" /><span>Dossiers</span>
                                    </x-nav-link>
                                </x-nav-link-container>
                            @endif
                    @endauth

                    {{-- Documents nav link --}}
                    @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_EMPLOYEE, \App\Models\User::ROLE_ADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/documents" :active="request()->is('documents')">
                                        <x-phosphor-folder-bold class="w-6 h-6 mr-3" /><span>Documenten</span>
                                    </x-nav-link>
                                </x-nav-link-container>
                            @endif
                    @endauth

                
                    {{-- Support nav link --}}
                    @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_EMPLOYEE, \App\Models\User::ROLE_ADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/support" :active="request()->is('support')">
                                        <x-phosphor-question-bold class="w-6 h-6 mr-3" /><span>Ondersteuning</span>
                                    </x-nav-link>
                                </x-nav-link-container>
                            @endif
                    @endauth

                    {{-- Add superdashboard nav link --}}
                     @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_SUPERADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/superdashboard" :active="request()->is('superdashboard')">
                                        <x-phosphor-house-bold class="w-6 h-6 mr-3" /><span>Dashboard</span>
                                    </x-nav-link>
                                </x-nav-link-container>
                            @endif
                    @endauth

                    {{-- Add organisation nav link --}}
                    @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_SUPERADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/organisations" :active="request()->is('organisations')">
                                        <x-phosphor-buildings-bold class="w-6 h-6 mr-3" /><span>Organisaties</span>
                                    </x-nav-link>
                                </x-nav-link-container>
                            @endif
                    @endauth
                </ul>
            </div>

            <div>
                {{-- Admin nav link --}}
                <ul>
                    @auth
                        @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_ADMIN]))
                            <x-nav-link-container>
                                <x-nav-link href="/admin" :active="request()->is('admin')">
                                    <x-phosphor-person-bold class="w-6 h-6 mr-3" />
                                    <span>Admin</span>
                                </x-nav-link>
                            </x-nav-link-container>
                        @endif
                    @endauth
                </ul>
                {{-- Logout button --}}
                <form method="POST" action="/logout">
                    @csrf
                    <x-nav-link type="button"><x-phosphor-sign-out-bold class="w-6 h-6 mr-3" /><span>Afmelden</span>
                    </x-nav-link>
                </form>
            </div>
        </nav>

        {{-- Mobile Navigation --}}
        <nav x-data="{ open: false }" class="lg:hidden w-full fixed top-0 left-0 z-50">
            <!-- Top Bar: Logo left, Hamburger right -->
            <div class="flex items-center justify-between w-full bg-white border-b border-gray-200 px-4 py-2">
                <picture>
                    <source srcset="{{ asset('images/Logo_LOEPOS_1.webp') }}" type="image/webp">
                    <source srcset="{{ asset('images/Logo_LOEPOS_1.png') }}" type="image/png">
                    <img src="{{ asset('images/Logo_LOEPOS_1.png') }}" alt="Loepos logo" class="h-10">
                </picture>
                <button @click="open = true"
                    class="p-2 focus:outline-none"
                    aria-label="Open menu">
                    <i class="bi bi-list text-3xl"></i>
                </button>
            </div>

            <!-- Fullscreen Overlay Menu -->
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-white bg-opacity-95 flex flex-col justify-between z-50"
                @click.away="open = false"
                style="display: none;"
            >
                <div class="flex justify-between items-center p-4 border-b border-gray-200">
                    <picture>
                        <source srcset="{{ asset('images/Logo_LOEPOS_1.webp') }}" type="image/webp">
                        <source srcset="{{ asset('images/Logo_LOEPOS_1.png') }}" type="image/png">
                        <img src="{{ asset('images/Logo_LOEPOS_1.png') }}" alt="Loepos logo" class="h-10">
                    </picture>
                    <button @click="open = false" class="text-3xl focus:outline-none" aria-label="Close menu">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <ul class="flex flex-col gap-6 text-xl px-8 py-8 flex-1 overflow-y-auto">
                    @auth
                        @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_EMPLOYEE, \App\Models\User::ROLE_ADMIN]))
                            <li>
                                <a href="/" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-house-bold class="w-6 h-6" /> Home
                                </a>
                            </li>
                            <li>
                                <a href="/dossiers" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-mailbox-bold class="w-6 h-6" /> Dossiers
                                </a>
                            </li>
                            <li>
                                <a href="/documents" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-folder-bold class="w-6 h-6" /> Documenten
                                </a>
                            </li>
                            <li>
                                <a href="/reports" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-chart-bar-bold class="w-6 h-6" /> Rapporten
                                </a>
                            </li>
                            <li>
                                <a href="/support" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-question-bold class="w-6 h-6" /> Ondersteuning
                                </a>
                            </li>
                        @endif
                        @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_SUPERADMIN]))
                            <li>
                                <a href="/superdashboard" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-house-bold class="w-6 h-6" /> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="/organisations" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-buildings-bold class="w-6 h-6" /> Organisaties
                                </a>
                            </li>
                        @endif
                        @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_ADMIN]))
                            <li>
                                <a href="/admin" @click="open = false" class="flex items-center gap-3">
                                    <x-phosphor-person-bold class="w-6 h-6" /> Admin
                                </a>
                            </li>
                        @endif
                        <li>
                            <form method="POST" action="/logout" @submit="open = false">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full text-left">
                                    <x-phosphor-sign-out-bold class="w-6 h-6" /> Afmelden
                                </button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </nav>

        {{-- Overlay for small screens --}}
        <div class="fixed inset-0 z-[9999] bg-white bg-opacity-95 flex flex-col items-center justify-center text-center px-6 sm:hidden">
            <div class="max-w-xs mx-auto">
                <picture>
                    <source srcset="{{ asset('images/Logo_LOEPOS_1.webp') }}" type="image/webp">
                    <source srcset="{{ asset('images/Logo_LOEPOS_1.png') }}" type="image/png">
                    <img src="{{ asset('images/Logo_LOEPOS_1.png') }}" alt="Loepos logo" class="h-16 mb-6 mx-auto">
                </picture>
                <h2 class="text-2xl font-bold mb-4">Gebruik een groter scherm</h2>
                <p class="text-lg text-dark-gray mb-2">Deze applicatie is niet beschikbaar op kleine schermen.</p>
                <p class="text-dark-gray">Gebruik een tablet, laptop of desktop voor volledige functionaliteit.</p>
            </div>
        </div>

        {{-- Main --}}
        <main class="pt-6 pb-2 px-14 flex-1 flex flex-col gap-8 overflow-y-auto
            lg:pt-6
            pt-[94px]
            pb-[36px]">
            {{ $slot }}
        </main>

        {{-- Additional Scripts --}}
        @stack('scripts')
    </body>
</html>
