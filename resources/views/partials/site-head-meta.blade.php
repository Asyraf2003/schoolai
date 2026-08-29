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
    'resources/css/pages/welcome-hero-visual.css',
    'resources/css/pages/welcome-scroll-reveal.css',
    'resources/js/pages/welcome-scroll-reveal.js',
  ])
@endif
