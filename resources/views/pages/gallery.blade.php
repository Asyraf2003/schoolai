<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.seo-head', [
        'seoTitle' => __('pages.common.nav.galeri').' · Al Mustaqbal School',
        'seoDescription' => __('gallery_v3.intro'),
        'seoCanonical' => route('galeri').(request()->integer('page', 1) > 1 ? '?page='.request()->integer('page') : ''),
        'seoIndexable' => true,
    ])
    @vite(['resources/css/gallery-page.css', 'resources/js/gallery-page.js'])
    @include('partials.public-transition-head')
</head>
<body class="gallery-page" data-portal-page="gallery">
    @include('partials.public-transition', ['transitionLogo' => $navigation['logoImageUrl']])
    <a class="skip-link" href="#main-content">{{ __('home.accessibility.skip_to_content') }}</a>
    @include('pages.partials.gallery-header')
    <main id="main-content" tabindex="-1">
        <section class="gallery-hero gallery-wrap" aria-labelledby="gallery-title">
            <div class="gallery-hero__eyebrow"><span>AL MUSTAQBAL SCHOOL</span><span>{{ __('gallery_v3.archive') }} / 01</span></div>
            <h1 id="gallery-title">{{ __('gallery_v3.title') }}<span>{{ __('gallery_v3.title_end') }}<i aria-hidden="true">✳</i></span></h1>
            <div class="gallery-hero__bottom"><p>{{ __('gallery_v3.intro') }}</p><a href="#all-posts">{{ __('gallery_v3.explore') }} <span aria-hidden="true">↘</span></a></div>
            <div class="gallery-collections">
                <div><h2>{{ __('gallery_v3.stories') }}</h2><p>{{ __('gallery_v3.stories_note') }}</p></div>
                <div class="gallery-collections__items">
                    @forelse($collections as $item)
                        @if(\App\Support\PublicUrl::isSafe($item->media_url))
                            <a href="{{ $item->media_url }}" data-viewer-link data-media-kind="photo" data-caption="{{ $item->alt_text ?: __('gallery_v3.collection') }}">
                                <img src="{{ \App\Support\PublicUrl::normalize((string) $item->thumbnail_url) ?: $item->media_url }}" alt="{{ $item->alt_text ?: __('gallery_v3.collection') }}" width="120" height="120" loading="lazy">
                                <span>{{ __('gallery_v3.collection') }} {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </a>
                        @endif
                    @empty
                        <p class="gallery-empty">{{ __('gallery_v3.stories_empty') }}</p>
                    @endforelse
                </div>
            </div>
        </section>
        @include('pages.partials.gallery-section', ['sectionId' => 'all-posts', 'number' => '01', 'label' => __('gallery_v3.all'), 'description' => __('gallery_v3.all_note'), 'paginator' => $items, 'composition' => 'editorial'])
        @if($instagramPosts->count())
            @include('pages.partials.gallery-section', ['sectionId' => 'instagram', 'number' => '↗', 'label' => __('instagram_gallery.instagram'), 'description' => __('gallery_v3.instagram_note'), 'paginator' => $instagramPosts, 'composition' => 'photos'])
        @endif
        @include('pages.partials.gallery-section', ['sectionId' => 'photos', 'number' => '02', 'label' => __('gallery_v3.photos'), 'description' => __('gallery_v3.photos_note'), 'paginator' => $photos, 'composition' => 'photos'])
        @include('pages.partials.gallery-section', ['sectionId' => 'films', 'number' => '03', 'label' => __('gallery_v3.films'), 'description' => __('gallery_v3.films_note'), 'paginator' => $videos, 'composition' => 'films'])
    </main>
    <footer class="gallery-footer gallery-wrap">
        <p>AL MUSTAQBAL SCHOOL <span>© {{ date('Y') }}</span></p>
        @if($ppdbUrl)<a href="{{ $ppdbUrl }}">{{ __('pages.common.nav.ppdb') }}</a>@endif
        <div class="gallery-locales" aria-label="{{ __('pages.common.language_label') }}">
            @foreach(['id' => 'ID', 'en' => 'EN', 'ar' => 'العربية'] as $locale => $name)
                <form method="POST" action="{{ route('language.switch', $locale) }}">@csrf<button type="submit" lang="{{ $locale }}" @if(app()->getLocale() === $locale) aria-current="true" @endif>{{ $name }}</button></form>
            @endforeach
        </div>
    </footer>
    @include('pages.partials.gallery-viewer')
</body>
</html>
