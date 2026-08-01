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

@if ($headLanguage !== 'ar')
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap"
  />
@endif

@if (request()->routeIs('home'))
  <link rel="stylesheet" href="{{ asset('css/welcome-gallery-desktop.css') }}" />
@endif

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

@include('partials.site-head-meta.base-styles')

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
