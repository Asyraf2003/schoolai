<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo-head', [
        'seoTitle' => __('home.meta.title'),
        'seoDescription' => __('home.meta.description'),
        'seoCanonical' => route('home'),
        'seoIsHome' => true,
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
    @vite(['resources/css/index.css', 'resources/js/index.js'])
</head>
<body class="landing">
    <a class="skip-link" href="#main-content">{{ __('home.accessibility.skip_to_content') }}</a>
    <div class="scroll-indicator" data-scroll-indicator aria-hidden="true">
        <span class="scroll-indicator__thumb" data-scroll-indicator-thumb></span>
    </div>
    @include('landing.header', $navigation)
    <main id="main-content" tabindex="-1">
        @include('landing.hero', ['hero' => $hero])
        @include('landing.about', ['about' => $about])
        <div class="program-values" data-program-values>
            @include('landing.program', ['program' => $program])
            @include('landing.values', ['values' => $values])
        </div>
        @include('landing.gallery', ['gallery' => $gallery])
        @include('landing.testimonials', ['testimonials' => $testimonials])
        @include('landing.stories')
    </main>
    @include('landing.footer')
</body>
</html>
