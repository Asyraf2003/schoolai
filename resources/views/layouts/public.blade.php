<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.site-head-meta', [
      'pageTitle' => $title ?? __('pages.common.site_title'),
      'pageDescription' => $description ?? __('pages.common.site_description'),
    ])
    @vite([
      'resources/css/pages/welcome.css',
      'resources/css/pages/welcome-hero.css',
      'resources/js/pages/welcome.js',
      'resources/js/pages/welcome-hero.js',
    ])
    @stack('head')
  </head>
  <body class="public-page-body public-content-page nav-shell">
    <a href="#main-content" class="skip-link">{{ __('pages.common.skip') }}</a>

    @include('partials.site-navbar')

    <main id="main-content" class="public-main">
      @yield('content')
    </main>

    @include('partials.site-footer')
  </body>
</html>
