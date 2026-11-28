<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $seoPage = max(1, request()->integer('page', 1));
        $seoCanonical = route($pageKey).($seoPage > 1 ? '?page='.$seoPage : '');
    @endphp
    @include('partials.seo-head', [
        'seoTitle' => $pageTitle.' · Al Mustaqbal School',
        'seoDescription' => __('home.meta.description'),
        'seoCanonical' => $seoCanonical,
        'seoIndexable' => true,
        'seoType' => 'website',
        'seoImage' => config('media.static.seo.home_og'),
    ])
    <script nonce="{{ Vite::cspNonce() }}">
        document.documentElement.setAttribute('data-landing-pending', '');
        window.addEventListener('DOMContentLoaded', function () {
            document.documentElement.removeAttribute('data-landing-pending');
        }, { once: true });
    </script>
    @if (app()->getLocale() === 'ar')
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('node_modules/@fontsource/cairo/files/cairo-arabic-400-normal.woff2') }}">
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('node_modules/@fontsource/cairo/files/cairo-arabic-700-normal.woff2') }}">
    @else
        <link rel="preload" as="font" type="font/woff2" crossorigin href="{{ Vite::asset('resources/fonts/inter/inter-latin-variable.woff2') }}">
    @endif
    @vite(['resources/css/coming-soon.css', 'resources/js/coming-soon.js'])
</head>
<body class="coming-soon-page">
    <a class="skip-link" href="#main-content">{{ __('home.accessibility.skip_to_content') }}</a>
    @include('landing.header', $navigation)
    <main id="main-content" class="coming-soon" data-coming-soon="{{ $pageKey }}" tabindex="-1">
        <div class="coming-soon__aurora" aria-hidden="true"></div>
        <h1 class="sr-only">{{ $pageTitle }}</h1>
        <figure class="coming-soon__media">
            <img src="{{ config('media.static.status.coming_soon') }}"
                 alt="{{ $pageTitle }} — Segera Hadir / Coming Soon"
                 width="1448" height="1086" decoding="async" fetchpriority="high">
        </figure>
    </main>
</body>
</html>
