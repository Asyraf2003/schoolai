@php
    $seoIndexable = $seoIndexable ?? true;
    $seoIsHome = $seoIsHome ?? false;
    $seoType = $seoType ?? 'website';
    $seoImage = $seoImage ?? config('media.static.seo.home_og');
    $seoSiteName = __('pages.common.site_title');
    $seoLocale = match (app()->getLocale()) {
        'id' => 'id_ID',
        'ar' => 'ar_AR',
        default => 'en_US',
    };
@endphp
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="application-name" content="{{ $seoSiteName }}">
<meta name="theme-color" content="#137a4c">
<link rel="icon" type="image/x-icon" href="{{ config('media.static.brand.favicon') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ config('media.static.brand.apple_touch_icon') }}">
@if (! $seoIndexable)
    <meta name="robots" content="noindex, nofollow">
@else
    <link rel="canonical" href="{{ $seoCanonical }}">
    <meta property="og:type" content="{{ $seoType }}">
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:locale" content="{{ $seoLocale }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $seoCanonical }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:image:alt" content="{{ $seoArticleTitle ?? $seoSiteName }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    <meta name="twitter:image:alt" content="{{ $seoArticleTitle ?? $seoSiteName }}">
    @php
        $seoOrganization = [
            '@type' => 'School',
            '@id' => route('home').'#school',
            'name' => $seoSiteName,
            'url' => route('home'),
            'logo' => config('media.static.brand.logo_square'),
        ];
        $seoGraph = [
            $seoOrganization,
            [
                '@type' => 'WebSite',
                '@id' => route('home').'#website',
                'name' => $seoSiteName,
                'url' => route('home'),
                'inLanguage' => app()->getLocale(),
            ],
        ];
        if ($seoType === 'article') {
            $seoGraph[] = [
                '@type' => 'Article',
                'headline' => $seoArticleTitle ?? $seoTitle,
                'description' => $seoDescription,
                'mainEntityOfPage' => $seoCanonical,
                'image' => $seoImage,
                'inLanguage' => app()->getLocale(),
                'publisher' => ['@id' => route('home').'#school'],
            ];
        }
        $seoStructuredData = [
            '@context' => 'https://schema.org',
            '@graph' => $seoGraph,
        ];
    @endphp
    <script nonce="{{ Vite::cspNonce() }}" type="application/ld+json">{!! json_encode(
        $seoStructuredData,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ) !!}</script>
@endif
