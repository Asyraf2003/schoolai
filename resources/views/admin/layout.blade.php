<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Al Mustaqbal</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="icon" href="{{ config('media.static.brand.favicon') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin">
    <a class="skip-link" href="#main-content">Lewati navigasi</a>
    <div class="admin-shell">
        <x-admin.sidebar/>
        <div class="admin-workspace">
            <header class="admin-topbar">
                <span class="admin-topbar-label">AL MUSTAQBAL <span aria-hidden="true">/</span> PANEL ADMIN</span>
                <span class="admin-topbar-user">{{ auth()->user()?->name ?? 'Administrator' }}</span>
            </header>
            <main class="admin-main" id="main-content" tabindex="-1">
                <div class="admin-page-heading">
                    <p class="admin-eyebrow">PENGELOLAAN SEKOLAH</p>
                    <h1>@yield('title')</h1>
                    @hasSection('description')<p class="admin-page-description">@yield('description')</p>@endif
                </div>
                @if(session('success'))
                    <p role="status" class="admin-notice">{{ session('success') }}</p>
                @endif
                @if($errors->any())
                    <div role="alert" class="admin-error">
                        <strong>Periksa kembali data berikut:</strong>
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
