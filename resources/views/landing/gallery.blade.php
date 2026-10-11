<section class="gallery" id="galeri" data-gallery aria-labelledby="gallery-title">
    <div class="gallery__stage" data-gallery-stage>
        <header class="gallery__header">
            <h2 id="gallery-title">{{ $gallery['title'] }}</h2>
            <button class="gallery__pause" type="button" data-gallery-pause aria-pressed="false" hidden>{{ __('gallery_v2.pause') }}</button>
        </header>
        <div class="gallery__composition" data-gallery-composition>
            @foreach ($gallery['items'] as $item)
                <figure class="gallery__item" data-gallery-item data-media-type="{{ $item['type'] }}">
                    <img src="{{ $item['poster'] }}" alt="{{ $item['title'] }}" width="1600" height="900" loading="lazy" decoding="async" data-gallery-poster>
                    @if ($item['type'] === 'video')
                        <video data-gallery-video data-src="{{ $item['url'] }}" poster="{{ $item['poster'] }}" preload="none" muted loop playsinline aria-hidden="true"></video>
                    @endif
                    <figcaption class="sr-only">{{ $item['title'] }}</figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
