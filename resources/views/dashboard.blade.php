<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.dashboard.title') }}</title>
</head>
<body>
    <main>
        @if (session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <h1>{{ __('app.dashboard.heading') }}</h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">{{ __('app.dashboard.logout') }}</button>
        </form>
    </main>
</body>
</html>
