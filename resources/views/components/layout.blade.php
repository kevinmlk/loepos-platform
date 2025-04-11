<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Loepos</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-inter ">
        {{-- Navigation --}}
        <nav>
            <ul>
                <li><x-nav-link href="/" :active="request()->is('/')">Home</x-nav-link></li>
                <li><x-nav-link href="/post-processing" :active="request()->is('post-processing')">Postverwerking</x-nav-link></li>
            </ul>
        </nav>

        {{-- Header --}}
        <header>
            <h1>{{ $title }}</h1>
        </header>

        {{-- Main --}}
        <main>
            {{ $slot }}
        </main>
    </body>
</html>
