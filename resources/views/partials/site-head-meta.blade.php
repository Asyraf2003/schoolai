@php
  $headSiteName = 'Al Mustaqbal School';
  $headTitle = trim((string) ($pageTitle ?? $headSiteName));
  $headDescription = trim((string) ($pageDescription ?? ''));
  $headCanonicalUrl = request()->url();
  $headHomeUrl = route('home');
  $headLogoUrl = asset('media/home/logo.png');
  $headImageUrl = asset('media/home/og-home.jpg');
  $headLanguage = in_array(app()->getLocale(), ['id', 'en', 'ar'], true)
      ? app()->getLocale()
      : 'id';
  $headDirection = $headLanguage === 'ar' ? 'rtl' : 'ltr';
  $headLocale = match ($headLanguage) {
      'en' => 'en_US',
      'ar' => 'ar_AR',
      default => 'id_ID',
  };
  $headImageAlt = match ($headLanguage) {
      'en' => 'Al Mustaqbal School campus and learning environment',
      'ar' => 'حرم مدرسة المستقبل وبيئة التعلم',
      default => 'Lingkungan sekolah dan pembelajaran Al Mustaqbal School',
  };

  $headStructuredData = [
      '@context' => 'https://schema.org',
      '@graph' => [
          [
              '@type' => 'School',
              '@id' => $headHomeUrl . '#school',
              'name' => $headSiteName,
              'url' => $headHomeUrl,
              'logo' => [
                  '@type' => 'ImageObject',
                  'url' => $headLogoUrl,
                  'contentUrl' => $headLogoUrl,
                  'width' => 1080,
                  'height' => 1080,
              ],
              'image' => [
                  '@id' => $headImageUrl . '#primaryimage',
              ],
          ],
          [
              '@type' => 'ImageObject',
              '@id' => $headImageUrl . '#primaryimage',
              'url' => $headImageUrl,
              'contentUrl' => $headImageUrl,
              'caption' => $headImageAlt,
              'width' => 1200,
              'height' => 630,
          ],
          [
              '@type' => 'WebSite',
              '@id' => $headHomeUrl . '#website',
              'url' => $headHomeUrl,
              'name' => $headSiteName,
              'inLanguage' => ['id', 'en', 'ar'],
              'publisher' => [
                  '@id' => $headHomeUrl . '#school',
              ],
          ],
          [
              '@type' => 'WebPage',
              '@id' => $headCanonicalUrl . '#webpage',
              'url' => $headCanonicalUrl,
              'name' => $headTitle,
              'description' => $headDescription,
              'inLanguage' => $headLanguage,
              'isPartOf' => [
                  '@id' => $headHomeUrl . '#website',
              ],
              'about' => [
                  '@id' => $headHomeUrl . '#school',
              ],
              'primaryImageOfPage' => [
                  '@id' => $headImageUrl . '#primaryimage',
              ],
          ],
      ],
  ];
@endphp

<title>{{ $headTitle }}</title>
<meta name="description" content="{{ $headDescription }}" />
<meta name="application-name" content="{{ $headSiteName }}" />
<meta name="theme-color" content="#137a4c" />

<link rel="canonical" href="{{ $headCanonicalUrl }}" />
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />

<meta property="og:type" content="website" />
<meta property="og:site_name" content="{{ $headSiteName }}" />
<meta property="og:locale" content="{{ $headLocale }}" />
<meta property="og:title" content="{{ $headTitle }}" />
<meta property="og:description" content="{{ $headDescription }}" />
<meta property="og:url" content="{{ $headCanonicalUrl }}" />
<meta property="og:image" content="{{ $headImageUrl }}" />
<meta property="og:image:type" content="image/jpeg" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="{{ $headImageAlt }}" />

<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $headTitle }}" />
<meta name="twitter:description" content="{{ $headDescription }}" />
<meta name="twitter:image" content="{{ $headImageUrl }}" />
<meta name="twitter:image:alt" content="{{ $headImageAlt }}" />

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  document.documentElement.setAttribute('dir', @json($headDirection));
</script>

<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  html,
  body {
    width: 100%;
    max-width: 100%;
    overscroll-behavior-x: none;
  }

  html {
    overflow-x: hidden;
  }

  @supports (overflow: clip) {
    html,
    body {
      overflow-x: clip;
    }
  }

  .navbar.is-scrolled {
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
  }

  html[dir="rtl"] body {
    direction: rtl;
    text-align: start;
  }

  html[dir="ltr"] body {
    direction: ltr;
    text-align: start;
  }

  html[dir="rtl"] .nav-link::after {
    right: 0;
    left: auto;
  }

  html[dir="rtl"] .nilai-card,
  html[dir="rtl"] .site-footer__grid {
    text-align: right;
  }

  html[dir="rtl"] .misi-card:hover {
    transform: translateX(-6px);
  }

  html[dir="rtl"] .skip-link {
    right: -999px;
    left: auto;
    border-radius: 0 0 0 10px;
  }

  html[dir="rtl"] .skip-link:focus {
    right: 0;
    left: auto;
  }

  @media (min-width: 1025px) {
    .galeri-section > .container {
      padding-inline: 0;
    }

    .galeri-story {
      width: 100%;
      max-width: none;
      grid-template-columns: 2fr 7fr 2fr 7fr 2fr;
      gap: 0;
      align-items: start;
      --gallery-story-gap: 0px;
      --gallery-visual-shift-x: 0px;
    }

    .galeri-story::before {
      display: none;
    }

    .galeri-story__copy {
      grid-column: 4;
      grid-row: 1;
      width: 100%;
      max-width: none;
      padding-block: 8vh 14vh;
    }

    .galeri-story-card {
      min-height: 72vh;
      display: grid;
      grid-template-columns: minmax(38px, 0.5fr) minmax(0, 6.5fr);
      gap: 0;
      align-items: center;
      transition:
        opacity 0.58s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.58s cubic-bezier(0.22, 1, 0.36, 1),
        filter 0.58s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .galeri-story-card__content {
      width: 100%;
      max-width: none;
      gap: 16px;
    }

    .galeri-story-card h3,
    .galeri-story-card p {
      width: 100%;
      max-width: none;
    }

    .galeri-story-card h3 {
      font-size: clamp(1.9rem, 1.65vw + 1.08rem, 3rem);
    }

    .galeri-story-card p {
      font-size: clamp(1.12rem, 0.42vw + 1rem, 1.36rem);
      line-height: 1.78;
    }

    .galeri-story__visual {
      grid-column: 2;
      grid-row: 1;
      min-height: calc(100svh - var(--nav-h) - 120px);
      transform: none;
    }

    .galeri-story-visual__track {
      width: 100%;
      max-width: none;
      aspect-ratio: 1.5 / 1;
    }

    .galeri-story-visual__panel {
      transform: translateY(22px) scale(0.96);
      transition:
        opacity 0.78s cubic-bezier(0.22, 1, 0.36, 1),
        transform 0.78s cubic-bezier(0.22, 1, 0.36, 1),
        filter 0.78s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .galeri-story-visual__panel.is-active {
      transform: translateY(0) scale(1);
    }

    .galeri-story-visual__media {
      border-radius: 32px;
    }

    .galeri-story-visual__badge {
      left: auto;
      right: auto;
      inset-inline-start: 18px;
    }

    .galeri-story-visual__play {
      left: auto;
      right: auto;
      inset-inline-end: 18px;
    }

    html[dir="rtl"] .galeri-story__copy {
      grid-column: 2;
    }

    html[dir="rtl"] .galeri-story__visual {
      grid-column: 4;
      transform: none;
    }
  }

  @media (max-width: 720px) {
    html[dir="ltr"] .navbar__menu {
      right: 0;
      left: auto;
      transform: translateX(105%);
    }

    html[dir="rtl"] .navbar__menu {
      right: auto;
      left: 0;
      align-items: stretch;
      transform: translateX(-105%);
      box-shadow: 12px 0 30px rgba(0, 0, 0, 0.12);
    }

    html[dir] .navbar__menu.active {
      transform: translateX(0);
    }

    html[dir] .navbar__menu ul {
      width: 100%;
      align-items: stretch;
    }

    html[dir="rtl"] .navbar__menu ul {
      text-align: right;
    }

    html[dir="ltr"] .navbar__menu ul {
      text-align: left;
    }

    html[dir] .navbar__cta {
      width: 100%;
    }
  }
</style>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" type="application/ld+json">{!! json_encode(
    $headStructuredData,
    JSON_UNESCAPED_SLASHES
    | JSON_UNESCAPED_UNICODE
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}</script>

@if (request()->routeIs('home'))
  @vite([
    'resources/css/pages/welcome-scroll-reveal.css',
    'resources/js/pages/welcome-scroll-reveal.js',
  ])
@endif
