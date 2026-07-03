<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.dashboard.title') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="dashboard-page" data-page="dashboard">
    <main>
        @if (session('success'))
            <p class="flash-message">{{ session('success') }}</p>
        @endif

        <h1>{{ __('app.dashboard.heading') }}</h1>

        <p>
            <a href="{{ route('admin.stats.edit') }}">Kelola Statistik Homepage</a>
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">{{ __('app.dashboard.logout') }}</button>
        </form>
    </main>
</body>
</html>
