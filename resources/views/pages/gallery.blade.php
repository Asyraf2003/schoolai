@extends('portal.shell')
@section('title', __('instagram_gallery.title'))

@section('content')
<section class="portal-gallery" data-portal-page="gallery">
    <p class="eyebrow">Al Mustaqbal School</p>
    <h1>{{ __('instagram_gallery.title') }}</h1>
    <p class="portal-gallery__intro">{{ __('instagram_gallery.intro') }}</p>

    @if($instagramPosts->count())
        <section aria-labelledby="instagram-gallery-heading" class="portal-gallery__section">
            <div class="portal-gallery__heading">
                <h2 id="instagram-gallery-heading">{{ __('instagram_gallery.instagram') }}</h2>
                <a href="https://www.instagram.com/asyrafm1/" target="_blank" rel="noopener noreferrer">@asyrafm1 ↗</a>
            </div>
            <div class="portal-gallery__grid">
                @foreach($instagramPosts as $post)
                    <article class="portal-gallery__card">
                        @if($post->is_video)
                            <video src="{{ $post->media_url }}" @if($post->thumbnail_url) poster="{{ $post->thumbnail_url }}" @endif controls preload="none" playsinline aria-label="{{ $post->caption ?: __('instagram_gallery.video') }}"></video>
                        @else
                            <a class="portal-gallery__image-link" href="{{ $post->permalink }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('instagram_gallery.open_post') }}">
                                <img src="{{ $post->thumbnail_url ?: $post->media_url }}" loading="lazy" decoding="async" alt="{{ \Illuminate\Support\Str::limit($post->caption ?: __('instagram_gallery.photo'), 200) }}">
                            </a>
                        @endif
                        <div class="portal-gallery__info">
                            <span class="portal-gallery__kind">{{ $post->is_video ? __('instagram_gallery.video') : __('instagram_gallery.photo') }}</span>
                            @if($post->caption)<p>{{ \Illuminate\Support\Str::limit($post->caption, 170) }}</p>@endif
                            <a href="{{ $post->permalink }}" target="_blank" rel="noopener noreferrer">{{ __('instagram_gallery.open_post') }} ↗</a>
                        </div>
                    </article>
                @endforeach
            </div>
            @if($instagramPosts->hasPages())
                <nav class="portal-gallery__pagination" aria-label="{{ __('instagram_gallery.instagram') }}">
                    @if($instagramPosts->previousPageUrl())<a href="{{ $instagramPosts->previousPageUrl() }}" rel="prev">← {{ __('instagram_gallery.previous') }}</a>@endif
                    <span>{{ $instagramPosts->currentPage() }} / {{ $instagramPosts->lastPage() }}</span>
                    @if($instagramPosts->nextPageUrl())<a href="{{ $instagramPosts->nextPageUrl() }}" rel="next">{{ __('instagram_gallery.next') }} →</a>@endif
                </nav>
            @endif
        </section>
    @endif

    @if($schoolMedia->isNotEmpty())
        <section aria-labelledby="school-gallery-heading" class="portal-gallery__section">
            <div class="portal-gallery__heading"><h2 id="school-gallery-heading">{{ __('instagram_gallery.school_media') }}</h2></div>
            <div class="portal-gallery__grid">
                @foreach($schoolMedia as $item)
                    <article class="portal-gallery__card">
                        @if($item->is_video)
                            <video src="{{ $item->media_url }}" @if($item->thumbnail_url) poster="{{ $item->thumbnail_url }}" @endif controls preload="none" playsinline aria-label="{{ $item->alt_text ?: __('instagram_gallery.video') }}"></video>
                        @else
                            <a class="portal-gallery__image-link" href="{{ $item->media_url }}" target="_blank" rel="noopener noreferrer">
                                <img src="{{ $item->thumbnail_url ?: $item->media_url }}" loading="lazy" decoding="async" alt="{{ $item->alt_text ?: __('instagram_gallery.photo') }}">
                            </a>
                        @endif
                        @if($item->alt_text)<p class="portal-gallery__caption">{{ $item->alt_text }}</p>@endif
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    @if(! $instagramPosts->count() && $schoolMedia->isEmpty())
        <p class="portal-gallery__empty">{{ __('instagram_gallery.empty') }}</p>
    @endif
</section>
@endsection
