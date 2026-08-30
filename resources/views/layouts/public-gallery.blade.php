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
      'resources/css/text-system.css',
      'resources/css/arabic-typography.css',
      'resources/css/public-latin-inter.css',
      'resources/js/pages/welcome.js',
      'resources/js/pages/welcome-hero.js',
    ])
    @stack('head')
  </head>
  <body class="public-page-body public-content-page site-cursor-page nav-shell gallery-page-body">
    <div id="gallery-perspective" class="gallery-perspective effect-airbnb" data-gallery-perspective>
      <div class="gallery-perspective__page" data-gallery-perspective-stage>
        <div class="gallery-perspective__wrapper" data-gallery-perspective-wrapper>
          <a href="#main-content" class="skip-link">{{ __('pages.common.skip') }}</a>

          @include('partials.site-navbar')

          <main id="main-content" class="public-main">
            @yield('content')
          </main>

          @include('partials.site-footer')
        </div>
      </div>

      @yield('gallery-navigation')
    </div>

    @yield('gallery-overlay')
  </body>
</html>
