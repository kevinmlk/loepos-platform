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
        <nav class="max-w-xs h-screen border-r-2 py-6 px-4 border-light-gray flex flex-col justify-between flex-shrink-0">
            <div class="flex flex-col items-start gap-6">
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

                    {{-- Reports nav link --}}
                    @auth
                            @if (in_array(auth()->user()->role, [\App\Models\User::ROLE_EMPLOYEE, \App\Models\User::ROLE_ADMIN]))
                                <x-nav-link-container>
                                    <x-nav-link href="/reports" :active="request()->is('reports')">
                                        <x-phosphor-chart-bar-bold class="w-6 h-6 mr-3" /><span>Rapporten</span>
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

        {{-- Main --}}
        <main class="pt-6 pb-2 px-14 flex-1 flex flex-col gap-8 h-screen overflow-hidden">
            {{ $slot }}
        </main>

        {{-- Additional Scripts --}}
        @stack('scripts')
    </body>
</html>
