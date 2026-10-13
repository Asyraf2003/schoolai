@if(\App\Support\PublicUrl::isSafe($item->media_url))
    @php
        $thumbnail = \App\Support\PublicUrl::normalize((string) $item->thumbnail_url);
        $caption = $item->alt_text ?: ($item->caption ?: ($item->is_video ? __('pages.common.gallery_video_title') : __('pages.common.media_photo')));
    @endphp
    <figure class="gallery-card" data-gallery-card>
        <div class="gallery-card__media">
            @if($item->is_video)
                <video controls playsinline preload="none" muted loop src="{{ $item->media_url }}" @if($thumbnail) poster="{{ $thumbnail }}" @endif aria-label="{{ $caption }}" data-gallery-video></video>
                <a class="gallery-card__expand" href="{{ $item->media_url }}" data-viewer-link data-media-kind="video" data-poster="{{ $thumbnail }}" data-caption="{{ $caption }}" aria-label="{{ __('gallery_v3.open') }}: {{ $caption }}"><span aria-hidden="true">↗</span></a>
            @else
                <a href="{{ $item->media_url }}" data-viewer-link data-media-kind="photo" data-caption="{{ $caption }}">
                    <img src="{{ $thumbnail ?: $item->media_url }}" alt="{{ $caption }}" width="1200" height="900" loading="{{ $priority ? 'eager' : 'lazy' }}" decoding="async">
                    <span class="gallery-card__expand" aria-hidden="true">↗</span>
                </a>
            @endif
        </div>
        <figcaption><span class="gallery-card__index">{{ str_pad((string) $position, 2, '0', STR_PAD_LEFT) }}</span><p>{{ \Illuminate\Support\Str::limit($caption, 160) }}</p><span>{{ $item->is_video ? __('gallery_v3.film') : __('gallery_v3.photo') }}</span></figcaption>
        @if($item->permalink && \App\Support\PublicUrl::isSafe($item->permalink))
            <a class="gallery-card__source" href="{{ $item->permalink }}" target="_blank" rel="noopener noreferrer">{{ __('instagram_gallery.open_post') }} ↗</a>
        @endif
    </figure>
@endif
