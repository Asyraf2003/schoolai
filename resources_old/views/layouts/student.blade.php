<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Dashboard Murid' }}</title>
    @vite([
        'resources/css/app.css',
        'resources/css/pages/student-dashboard.css',
        'resources/js/pages/student-account.js',
    ])
</head>
<body class="student-shell">
    <header class="student-header">
        <a class="student-brand" href="{{ route('murid.dashboard') }}">Al Mustaqbal</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="student-logout" type="submit">Keluar</button>
        </form>
    </header>

    <div class="student-frame">
        <nav class="student-nav" aria-label="Menu murid">
            <a
                href="{{ route('murid.dashboard') }}"
                @if(request()->routeIs('murid.dashboard')) aria-current="page" @endif
            >Dashboard</a>
            <a
                href="{{ route('murid.account') }}"
                @if(request()->routeIs('murid.account')) aria-current="page" @endif
            >Pengaturan Akun</a>
        </nav>

        <main class="student-main" id="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
