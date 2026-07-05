{{-- PUBLIC_LAYOUT_DUMMY_FINAL --}}
<!doctype html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title ?? __('pages.common.site_title') }}</title>
    <meta name="description" content="{{ $description ?? __('pages.common.site_description') }}" />
    @vite(['resources/css/pages/welcome.css', 'resources/js/pages/welcome.js'])
  </head>
  <body class="public-page-body">
    <a href="#main-content" class="skip-link">{{ __('pages.common.skip') }}</a>

    @include('partials.site-navbar')

    <main id="main-content" class="public-main">
      @yield('content')
    </main>

    @include('partials.site-footer')
  </body>
</html>
