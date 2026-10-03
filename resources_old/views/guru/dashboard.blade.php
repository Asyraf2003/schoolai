<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Guru</title>
    @vite(['resources/css/app.css', 'resources/css/pages/account-locked.css'])
</head>
<body class="account-locked-page">
    <main class="account-locked-card" aria-labelledby="guru-dashboard-title">
        <p class="account-locked-kicker">Akun Guru Aktif</p>
        <h1 id="guru-dashboard-title">Dashboard Guru</h1>
        <p class="account-locked-description">
            Akun Anda aktif. Fitur akademik guru belum termasuk dalam ruang lingkup saat ini.
        </p>
        <div class="account-locked-user">
            <strong>{{ auth()->user()->name }}</strong>
            <span>{{ auth()->user()->email }}</span>
        </div>
        <form class="account-locked-actions" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="account-locked-button">Keluar</button>
        </form>
    </main>
</body>
</html>
