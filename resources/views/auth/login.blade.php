<!-- resources/views/auth/login.blade.php -->

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <title>Aanmelden - Loepos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

  <!-- Styles / Scripts -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-inter text-black flex min-h-screen bg-white">

  <!-- Left Side: Login Form -->
  <div class="w-full md:w-1/2 flex items-center justify-center px-6">
    <div class="w-full max-w-md space-y-6">
      <div class="mb-8">
        <picture>
            <source srcset="{{ asset('images/Logo_LOEPOS_1.webp') }}" type="image/webp">
            <source srcset="{{ asset('images/Logo_LOEPOS_1.png') }}" type="image/png">
            <img src="{{ asset('images/Logo_LOEPOS_1.png') }}" alt="Loepos logo" class="h-14 mb-8">
        </picture>
        <h2 class="text-4xl font-semibold mb-3">Aanmelden</h2>
        <p class="text-base text-dark-gray">Welkom terug! Voer uw gegevens in.</p>
      </div>

      <form method="POST" action="/login" class="space-y-5">
        @csrf
        <div>
          <x-form.label for="email">Email</x-form.label>
          <x-form.input id="email" type="email" name="email" placeholder="mail@example.com" required autofocus />
          <x-form.error name="email" />
        </div>

        <div>
          <x-form.label for="password">Wachtwoord</x-form.label>
          <x-form.input id="password" type="password" name="password" placeholder="***********" required autofocus />
          <x-form.error name="password" />
        </div>

        <div class="flex items-center justify-between">
          <x-form.radio id="remember" name="remember">Onthoud mij</x-form.radio>
          <a href="" class="text-button font-semibold text-blue hover:text-dark-blue">
            Wachtwoord vergeten
          </a>
        </div>

        <x-ui.button class="w-full justify-center" type="primary">Aanmelden</x-ui.button>
      </form>

      <p class="text-xs text-gray-400 text-center mt-10">© Loepos 2025</p>
    </div>
  </div>

  <!-- Right Side: Preview (optional for aesthetics only) -->
  <div class="hidden md:flex w-1/2 bg-blue items-center justify-end">
    <picture class="w-4/5">
        <source srcset="{{ asset('images/macbook-mockup.webp') }}" type="image/webp">
        <source srcset="{{ asset('images/macbook-mockup.png') }}" type="image/png">
        <img src="{{ asset('images/mackbook-mockup.png') }}" alt="Dashboard preview">
    </picture>
  </div>

</body>
</html>
