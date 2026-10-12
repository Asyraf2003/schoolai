<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') · Al Mustaqbal School</title>
    <link rel="icon" href="{{ config('media.static.brand.favicon') }}">
    @vite('resources/css/portal.css')
    <style nonce="{{ Vite::cspNonce() }}">
        :root { color-scheme: light; font-family: Inter, system-ui, sans-serif; }
        html[dir="rtl"] { font-family: Cairo, Tahoma, sans-serif; }
        * { box-sizing: border-box; }
        body { margin: 0; min-block-size: 100svh; background: #faf9f6; color: #17251e; }
        a { color: inherit; }
        button, input { font: inherit; }
        button, .action { display: inline-flex; align-items: center; justify-content: center; min-block-size: 44px; padding: .7rem 1.1rem; border-radius: .65rem; border: 1px solid #234b39; background: #234b39; color: #fff; text-decoration: none; cursor: pointer; }
        button:hover, .action:hover { background: #163c2b; }
        a:focus-visible, button:focus-visible, input:focus-visible { outline: 3px solid #bc7a06; outline-offset: 3px; }
        .shell { inline-size: min(100% - 2rem, 58rem); margin-inline: auto; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding-block: 2rem; }
        header a { text-decoration: none; font-weight: 700; }
        main { padding-block: clamp(4rem, 12vh, 9rem); }
        .eyebrow { margin: 0 0 1rem; font-size: .85rem; letter-spacing: .06em; text-transform: uppercase; color: #547162; }
        h1 { font-size: clamp(2rem, 7vw, 4rem); line-height: 1.1; margin: 0 0 1rem; }
        p { max-inline-size: 38rem; line-height: 1.7; }
        .actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-block-start: 2rem; }
        .secondary { color: #234b39; background: transparent; }
        .secondary:hover { background: #ebf1ec; }
        .choices { display: grid; grid-template-columns: repeat(auto-fit, minmax(min(100%, 12rem), 1fr)); gap: .75rem; margin-block-start: 1.5rem; }
        .choices a { border: 1px solid #d7e0da; padding: 1.4rem; text-decoration: none; border-radius: .8rem; background: white; font-weight: 650; }
        form.fields { display: grid; gap: 1rem; max-inline-size: 25rem; }
        .fields label { display: grid; gap: .4rem; }
        .fields input { border-radius: .6rem; border: 1px solid #849b8b; padding: .8rem; min-block-size: 44px; }
        .error { color: #a52323; }
        .logout { margin: 0; }
        @media (max-width: 480px) { header { align-items: start; } }
    </style>
</head>
<body>
    <header class="shell">
        <a href="{{ route('home') }}">Al Mustaqbal School</a>
        @auth
            <form class="logout" method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="secondary">{{ __('app.dashboard.logout') }}</button>
            </form>
        @endauth
    </header>
    <main class="shell" id="main-content">
        @yield('content')
    </main>
</body>
</html>
