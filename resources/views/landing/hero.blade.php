<section class="hero" id="beranda" data-hero tabindex="-1"
    aria-label="{{ $hero['section_label'] }}" data-interval="{{ $hero['autoplay_interval'] }}"
    data-slide-label="{{ $hero['slide_label'] }}">
    <div class="hero__slides">
        @foreach ($hero['slides'] as $slide)
            <article class="hero__slide" data-slide data-kind="{{ $slide['render_type'] }}"
                data-title="{{ $slide['title'] }}">
                <div class="hero__media" data-media-fallback>
                    <img src="{{ $slide['render_type'] === 'video' ? $slide['poster_url'] : $slide['media_url'] }}"
                        alt="{{ $slide['media_alt'] }}" width="1920" height="1080" decoding="async"
                        @if ($loop->first) fetchpriority="high" loading="eager" @else loading="lazy" @endif>
                    @if ($slide['render_type'] === 'video')
                        <video data-video muted playsinline preload="none" aria-hidden="true" tabindex="-1"
                            poster="{{ $slide['poster_url'] }}">
                            <source data-src="{{ $slide['media_url'] }}" type="{{ $slide['video_mime_type'] }}">
                        </video>
                    @endif
                </div>
                <div class="hero__content">
                    @include('landing.hero-copy', ['slide' => $slide, 'primary' => $loop->first])
                </div>
            </article>
        @endforeach
    </div>
    <div class="hero__controls" data-hero-controls hidden>
        @if (count($hero['slides']) > 1)
            <button class="hero__arrow hero__arrow--previous" type="button" data-previous aria-label="{{ $hero['previous_label'] }}">
                <svg viewBox="0 0 128 72" aria-hidden="true"><path d="M42 4 10 36l32 32 14-14-18-18 18-18Z"/><path d="M78 4 46 36l32 32 14-14-18-18 18-18Z"/><path d="M114 4 82 36l32 32 14-14-18-18 18-18Z"/></svg>
            </button>
            <button class="hero__arrow hero__arrow--next" type="button" data-next aria-label="{{ $hero['next_label'] }}">
                <svg viewBox="0 0 128 72" aria-hidden="true"><path d="m14 4 32 32-32 32L0 54l18-18L0 18Z"/><path d="m50 4 32 32-32 32-14-14 18-18-18-18Z"/><path d="m86 4 32 32-32 32-14-14 18-18-18-18Z"/></svg>
            </button>
        @endif

    </div>
    <p class="sr-only" data-hero-live aria-live="polite" aria-atomic="true"></p>
</section>
