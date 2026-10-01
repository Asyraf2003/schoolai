<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('home.partials.opening-bootstrap')
    @include('partials.site-head-meta', [
      'pageTitle' => $meta['title'],
      'pageDescription' => $meta['description'],
    ])

    @if (app()->environment('production') && app()->getLocale() !== 'ar')
      <link
        rel="preload"
        href="{{ \Illuminate\Support\Facades\Vite::asset('resources/fonts/inter/inter-latin-variable.woff2') }}"
        as="font"
        type="font/woff2"
        crossorigin="anonymous"
        data-home-critical-font="inter"
      >
    @endif

    @if (app()->environment('production'))
      <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-home-critical-style="foundation">{!! \Illuminate\Support\Facades\Vite::content('resources/css/pages/welcome-critical.css') !!}</style>
    @else
      @vite('resources/css/pages/welcome-critical.css')
    @endif

    @vite([
      'resources/css/pages/welcome.css',
      'resources/css/pages/welcome-login-perspective.css',
    ])

    @if (app()->environment('production'))
      <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-home-critical-style="hero">{!! \Illuminate\Support\Facades\Vite::content('resources/css/pages/welcome-home-hero.css') !!}</style>
    @else
      @vite('resources/css/pages/welcome-home-hero.css')
    @endif

    @vite([
      'resources/css/pages/welcome-vision-waapi.css',
      'resources/css/pages/welcome-values-story.css',
      'resources/css/pages/welcome-depth-gallery.css',
      'resources/css/pages/welcome-article-showcase.css',
    ])

    @if (app()->getLocale() === 'ar')
      @if (app()->environment('production'))
        <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-home-critical-style="type">{!! \Illuminate\Support\Facades\Vite::content('resources/css/pages/welcome-home-type-arabic.css') !!}</style>
      @else
        @vite('resources/css/pages/welcome-home-type-arabic.css')
      @endif
    @else
      @if (app()->environment('production'))
        <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-home-critical-style="type">{!! \Illuminate\Support\Facades\Vite::content('resources/css/pages/welcome-home-type-latin.css') !!}</style>
      @else
        @vite('resources/css/pages/welcome-home-type-latin.css')
      @endif
    @endif

    @vite([
      'resources/css/pages/welcome-editorial-headings.css',
      'resources/css/pages/welcome-editorial-description-desktop.css',
      'resources/js/pages/welcome.js',
      'resources/js/pages/welcome-login-perspective.js',
      'resources/js/pages/welcome-hero.js',
      'resources/js/pages/welcome-editorial-headings.js',
    ])

    <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
      (() => {
        const activateDeferredStyles = () => {
          document.querySelectorAll('link[data-home-deferred-style]').forEach((stylesheet) => {
            stylesheet.media = 'all';
            stylesheet.removeAttribute('data-home-deferred-style');
          });
        };

        requestAnimationFrame(() => {
          requestAnimationFrame(activateDeferredStyles);
        });

        document.addEventListener('DOMContentLoaded', activateDeferredStyles, { once: true });
      })();
    </script>

    <noscript>
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-login-perspective.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-vision-waapi.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-values-story.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-depth-gallery.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-article-showcase.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-editorial-headings.css') }}">
      <link rel="stylesheet" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/pages/welcome-editorial-description-desktop.css') }}">
    </noscript>
  </head>
  <body class="home-page site-cursor-page nav-shell">
    <a href="#main-content" class="skip-link">{{ __('home.accessibility.skip_to_content') }}</a>

    @include('partials.site-navbar', ['navbar' => $navbar, 'siteNavMode' => 'home'])

    <main id="main-content">
      @include('home.sections.hero')

      @include('home.sections.vision-mission')

      <div class="program-values-world" data-program-values-world>
        <div class="program-values-world__visual" aria-hidden="true">
          <div class="program-values-world__kinetic">
            @foreach ($programValuesKineticLines as $line)
              <span class="program-values-world__kinetic-line">{{ $line }}</span>
            @endforeach
          </div>
        </div>

        @include('home.sections.featured-programs')

        @include('home.sections.school-values')
      </div>

      @include('home.sections.gallery')

      @include('home.sections.testimonials')

      @include('home.sections.articles')
    </main>

    @include('partials.site-footer', ['footerSection' => $footerSection])
    @include('home.partials.login-perspective-template')
  </body>
</html>