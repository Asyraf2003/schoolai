<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.site-head-meta', [
      'pageTitle' => $meta['title'],
      'pageDescription' => $meta['description'],
    ])
    @vite([
      'resources/css/pages/welcome.css',
      'resources/css/pages/welcome-hero.css',
      'resources/css/pages/welcome-vision-waapi.css',
      'resources/css/pages/welcome-values-story.css',
      'resources/css/pages/welcome-depth-gallery.css',
      'resources/css/text-system.css',
      'resources/css/arabic-typography.css',
      'resources/css/public-latin-inter.css',
      'resources/css/pages/welcome-editorial-headings.css',
      'resources/css/pages/welcome-editorial-description-desktop.css',
      'resources/js/pages/welcome.js',
      'resources/js/pages/welcome-hero.js',
      'resources/js/pages/welcome-editorial-headings.js',
    ])
  </head>
  <body class="home-page nav-shell">
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
    </main>

    @include('partials.site-footer', ['footerSection' => $footerSection])
  </body>
</html>
