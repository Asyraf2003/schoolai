@extends('portal.shell')
@section('title', __('pages.common.nav.galeri'))
@section('content')
    @include('pages.partials.navigation')
    <h1>{{ __('pages.common.nav.galeri') }}</h1>
    <div class="public-grid" data-portal-page="gallery" data-public-gallery>
        @forelse($items as $item)
            @if(\App\Support\PublicUrl::isSafe($item->media_url))
                @php($thumbnail = \App\Support\PublicUrl::normalize((string) $item->thumbnail_url))
                <figure class="public-card">
                    @if($item->is_video)
                        <video controls playsinline preload="none" src="{{ $item->media_url }}" @if($thumbnail) poster="{{ $thumbnail }}" @endif aria-label="{{ $item->alt_text ?: __('pages.common.gallery_video_title') }}"></video>
                    @else
                        <a href="{{ $item->media_url }}" aria-label="{{ $item->alt_text ?: __('pages.common.media_photo') }}"><img src="{{ $thumbnail ?: $item->media_url }}" alt="{{ $item->alt_text ?? '' }}" width="1200" height="675" loading="lazy" decoding="async"></a>
                    @endif
                </figure>
            @endif
        @empty
            <p>{{ __('public_content.gallery_empty') }}</p>
        @endforelse
    </div>
    @include('pages.partials.pagination', ['paginator' => $items])

    @if($instagramPosts->count())
        <section class="instagram-feed" aria-labelledby="instagram-gallery-heading">
            <div class="instagram-feed__heading">
                <h2 id="instagram-gallery-heading">{{ __('instagram_gallery.instagram') }}</h2>
                <a href="https://www.instagram.com/asyrafm1/" target="_blank" rel="noopener noreferrer">@asyrafm1 ↗</a>
            </div>
            <div class="public-grid" data-public-instagram-gallery>
                @foreach($instagramPosts as $post)
                    @if(\App\Support\PublicUrl::isSafe($post->media_url))
                        <figure class="public-card instagram-feed__card">
                            @if($post->is_video)
                                <video controls playsinline preload="none" src="{{ $post->media_url }}" @if(\App\Support\PublicUrl::isSafe($post->thumbnail_url)) poster="{{ $post->thumbnail_url }}" @endif aria-label="{{ $post->caption ?: __('instagram_gallery.video') }}"></video>
                            @else
                                <a href="{{ $post->permalink }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('instagram_gallery.open_post') }}">
                                    <img src="{{ $post->thumbnail_url ?: $post->media_url }}" alt="{{ \Illuminate\Support\Str::limit($post->caption ?: __('instagram_gallery.photo'), 200) }}" width="1200" height="675" loading="lazy" decoding="async">
                                </a>
                            @endif
                            <figcaption>
                                <span>{{ $post->is_video ? __('instagram_gallery.video') : __('instagram_gallery.photo') }}</span>
                                @if($post->caption)<p>{{ \Illuminate\Support\Str::limit($post->caption, 150) }}</p>@endif
                                <a href="{{ $post->permalink }}" target="_blank" rel="noopener noreferrer">{{ __('instagram_gallery.open_post') }} ↗</a>
                            </figcaption>
                        </figure>
                    @endif
                @endforeach
            </div>
            @if($instagramPosts->hasPages())
                <nav class="public-pagination" aria-label="{{ __('instagram_gallery.instagram') }}">
                    @if($instagramPosts->previousPageUrl())<a rel="prev" href="{{ $instagramPosts->previousPageUrl() }}">← {{ __('instagram_gallery.previous') }}</a>@endif
                    <span>{{ $instagramPosts->currentPage() }} / {{ $instagramPosts->lastPage() }}</span>
                    @if($instagramPosts->nextPageUrl())<a rel="next" href="{{ $instagramPosts->nextPageUrl() }}">{{ __('instagram_gallery.next') }} →</a>@endif
                </nav>
            @endif
        </section>
    @endif
@endsection
