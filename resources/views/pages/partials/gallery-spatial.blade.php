<section class="gallery-spatial" data-spatial-gallery aria-labelledby="gallery-title">
    <h1 class="sr-only" id="gallery-title">{{ __('pages.common.nav.galeri') }}</h1>
    <div class="gallery-spatial__viewport" data-spatial-viewport>
        <div class="gallery-spatial__track" data-spatial-track>
            @foreach($stageItems as $media)
                <a class="gallery-spatial__panel" href="{{ $media['url'] }}" data-spatial-panel data-viewer-link
                    data-media-kind="{{ $media['type'] }}" data-poster="{{ $media['poster'] }}" data-caption="{{ $media['title'] }}">
                    <img src="{{ $media['poster'] }}" alt="" width="1200" height="800" loading="{{ $loop->index < 3 ? 'eager' : 'lazy' }}" decoding="async" draggable="false">
                    <span class="gallery-spatial__caption">{{ $media['title'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
    <div class="gallery-spatial__floor" aria-hidden="true"></div>
    <div class="gallery-spatial__intro gallery-wrap">
        <p>{{ __('gallery_v3.intro') }}</p>
        <a href="#stories" aria-label="{{ __('gallery_v3.explore') }}">↓</a>
    </div>
</section>
