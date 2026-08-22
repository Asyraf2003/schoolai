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
      'resources/css/pages/welcome-article-story.css',
      'resources/css/text-system.css',
      'resources/css/arabic-typography.css',
      'resources/css/public-latin-inter.css',
      'resources/css/pages/welcome-editorial-headings.css',
      'resources/css/pages/welcome-editorial-description-desktop.css',
      'resources/js/pages/welcome.js',
      'resources/js/pages/welcome-hero.js',
      'resources/js/pages/welcome-vision-story.js',
      'resources/js/pages/welcome-depth-gallery.js',
      'resources/js/pages/welcome-editorial-headings.js',
    ])
  </head>
  <body class="home-page nav-shell">
    <a href="#main-content" class="skip-link">{{ __('home.accessibility.skip_to_content') }}</a>

    @include('partials.site-navbar', ['navbar' => $navbar, 'siteNavMode' => 'home'])

    <main id="main-content">
      @php
        $heroSlides = collect($hero['slides'] ?? [])->values();
        $heroSlideCount = $heroSlides->count();
        $heroStatus = static fn (int $current): string => strtr(
            (string) ($hero['slide_label'] ?? 'Slide :current / :total'),
            [
                ':current' => (string) $current,
                ':total' => (string) $heroSlideCount,
            ],
        );
      @endphp

      @include('home.sections.hero')

      @include('home.sections.vision-mission')

      @php
        $programValuesContent = trans('home_program');
        $programValuesWords = collect(is_array($programValuesContent)
            ? ($programValuesContent['kinetic_words'] ?? [])
            : [])
          ->filter(fn ($word) => is_string($word) && trim($word) !== '')
          ->values();
        $programValuesWordCount = max(1, $programValuesWords->count());
        $programValuesKineticLines = collect(range(0, 7))->map(function ($line) use (
            $programValuesWords,
            $programValuesWordCount,
        ) {
            $words = collect(range(0, 7))
              ->map(fn ($offset) => $programValuesWords[
                  ($line * 3 + $offset) % $programValuesWordCount
              ] ?? '')
              ->filter()
              ->implode(' ');

            return trim($words . ' ' . $words);
        })->filter();
      @endphp

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

      @include('home.sections.articles')
    </main>

    @include('partials.site-footer', ['footerSection' => $footerSection])
  </body>
</html>
