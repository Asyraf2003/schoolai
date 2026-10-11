<section class="gallery" id="galeri" data-gallery aria-labelledby="gallery-title">
    <header class="gallery__header">
        <h2 id="gallery-title" data-gallery-heading aria-label="{{ $gallery['title'] }}">
            <span class="gallery__heading-clip"><span data-gallery-heading-line aria-hidden="true">{{ $gallery['title'] }}</span></span>
        </h2>
    </header>
    <div class="gallery__stage" data-gallery-stage>
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
        <p class="gallery__statement" data-gallery-statement aria-label="{{ implode(' ', __('gallery_v2.statement_lines')) }}">
            @foreach (__('gallery_v2.statement_lines') as $line)
                <span class="gallery__statement-line" aria-hidden="true">@foreach (explode(' ', $line) as $word)<span data-gallery-word>{{ $word }}</span>{{ $loop->last ? '' : ' ' }}@endforeach</span>
            @endforeach
        </p>
    </div>
</section>
