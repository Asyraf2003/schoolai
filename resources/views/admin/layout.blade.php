<!doctype html>
<html lang="id" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · SchoolAI</title>
    <link rel="icon" href="{{ config('media.static.brand.favicon') }}">
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="admin">
    <a class="skip-link" href="#main-content">Lewati navigasi</a>
    <header class="admin-header">
        <a href="{{ route('home') }}">SchoolAI</a>
        <nav aria-label="Menu admin">
            <a href="{{ route('admin.accounts.index') }}" @if(request()->routeIs('admin.accounts.*', 'admin.dashboard')) aria-current="page" @endif>Akun</a>
            <a href="{{ route('admin.galeri') }}" @if(request()->routeIs('admin.galeri*')) aria-current="page" @endif>Galeri</a>
            <a href="{{ route('admin.artikel') }}" @if(request()->routeIs('admin.artikel*')) aria-current="page" @endif>Artikel</a>
            <a href="{{ route('admin.ppdb') }}" @if(request()->routeIs('admin.ppdb*')) aria-current="page" @endif>Link PPDB</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="secondary">Keluar</button></form>
    </header>
    <main class="admin-main" id="main-content" tabindex="-1">
        <h1>@yield('title')</h1>
        @if(session('success'))<p role="status" class="notice">{{ session('success') }}</p>@endif
        @if($errors->any())
            <div role="alert" class="error"><p>Periksa kembali data berikut:</p><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @yield('content')
    </main>
</body>
</html>
