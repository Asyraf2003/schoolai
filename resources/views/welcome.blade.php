<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.home.title') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="welcome-page">
        <section class="welcome-card" data-welcome-card>
            <div class="welcome-orb" aria-hidden="true"></div>

            <p class="welcome-kicker">{{ __('app.home.kicker') }}</p>
            <h1>{{ __('app.home.heading') }}</h1>
            <p class="welcome-note" data-welcome-note>
                {{ __('app.home.note') }}
            </p>
        </section>
    </main>
</body>
</html>
