<section
  class="hero-cinema"
  id="beranda"
  data-hero-slider
  data-autoplay-interval="{{ $hero['autoplay_interval'] ?? 7000 }}"
  data-slide-label="{{ $hero['slide_label'] ?? 'Slide :current / :total' }}"
  aria-label="{{ $hero['section_label'] ?? 'Al Mustaqbal School' }}"
  aria-roledescription="{{ $hero['carousel_roledescription'] ?? 'carousel' }}"
  tabindex="-1"
>
  <div class="hero-cinema__viewport">
    @foreach ($heroSlides as $slide)
      @php
        $slideId = 'hero-slide-'.$loop->index;
        $isVideo = ($slide['render_type'] ?? 'image') === 'video';
        $fallbackUrl = $slide['poster_url'] ?? $hero['fallback_image_url'] ?? null;
        $ultimateFallbackUrl = $slide['fallback_url'] ?? $hero['fallback_image_url'] ?? null;
      @endphp
      <article
        class="hero-cinema__slide{{ $loop->first ? ' is-active' : '' }}"
        id="{{ $slideId }}"
        data-hero-slide
        data-slide-index="{{ $loop->index }}"
        data-media-type="{{ $slide['type'] }}"
        data-slide-title="{{ $slide['title'] }}"
        role="region"
        aria-roledescription="{{ $hero['slide_roledescription'] ?? 'slide' }}"
        aria-label="{{ $heroStatus($loop->iteration) }}"
        aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
        @if (! $loop->first) inert @endif
      >
        <div
          class="hero-cinema__media"
          style="--hero-focal-position: {{ $slide['focal_position'] }}; --hero-overlay-strength: {{ $slide['overlay_strength'] }}"
        >
          @if ($isVideo)
            @if ($fallbackUrl)
              <img
                class="hero-cinema__poster"
                data-hero-poster
                data-fallback-src="{{ $ultimateFallbackUrl }}"
                src="{{ $fallbackUrl }}"
                crossorigin="anonymous"
                alt="{{ $slide['media_alt'] ?? '' }}"
                width="1920"
                height="1080"
                decoding="async"
                @if ($loop->first) fetchpriority="high" loading="eager" @else loading="lazy" @endif
              />
            @endif
            <video
              data-hero-video
              muted
              playsinline
              webkit-playsinline
              crossorigin="anonymous"
              preload="none"
              @if ($fallbackUrl) poster="{{ $fallbackUrl }}" @endif
              aria-hidden="true"
              tabindex="-1"
            >
              <source
                data-src="{{ $slide['media_url'] }}"
                type="{{ $slide['video_mime_type'] ?? 'video/mp4' }}"
              />
            </video>
          @else
            <img
              class="hero-cinema__image"
              data-hero-image
              data-fallback-src="{{ $ultimateFallbackUrl }}"
              crossorigin="anonymous"
              @if ($loop->first)
                src="{{ $slide['media_url'] }}"
                fetchpriority="high"
                loading="eager"
              @else
                data-src="{{ $slide['media_url'] }}"
                loading="lazy"
              @endif
              alt="{{ $slide['media_alt'] ?? '' }}"
              width="1920"
              height="1080"
              decoding="async"
            />
          @endif
          <span class="hero-cinema__media-error" aria-hidden="true"></span>
        </div>

        <div class="hero-cinema__content container">
          <div class="hero-cinema__copy">
            @if (! empty($slide['eyebrow']))
              <p class="hero-cinema__eyebrow" data-text-role="label">{{ $slide['eyebrow'] }}</p>
            @endif

            @if ($loop->first)
              <h1 class="hero-cinema__title" data-text-role="display">{{ $slide['title'] }}</h1>
            @else
              <h2 class="hero-cinema__title" data-text-role="display">{{ $slide['title'] }}</h2>
            @endif

            @if (! empty($slide['description']))
              <p class="hero-cinema__description" data-text-role="description">{{ $slide['description'] }}</p>
            @endif

            @if (! empty($slide['cta']['label']) && ! empty($slide['cta']['href']))
              <a href="{{ $slide['cta']['href'] }}" class="hero-cinema__cta" data-text-role="action">
                <span>{{ $slide['cta']['label'] }}</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </a>
            @endif
          </div>
        </div>
      </article>
    @endforeach
  </div>

  @if ($heroSlideCount > 1)
    <div class="hero-cinema__controls container" data-hero-controls>
      <div class="hero-cinema__progress" aria-hidden="true">
        <span class="hero-cinema__progress-bar" data-hero-progress></span>
      </div>

      <p class="hero-cinema__counter" aria-hidden="true">
        <span class="hero-cinema__counter-current" data-hero-current>01</span>
        <span>/</span>
        <span>{{ str_pad((string) $heroSlideCount, 2, '0', STR_PAD_LEFT) }}</span>
      </p>

      <div class="hero-cinema__dots" aria-label="{{ $hero['dots_label'] ?? 'Choose slide' }}">
        @foreach ($heroSlides as $slide)
          <button
            type="button"
            class="hero-cinema__dot{{ $loop->first ? ' is-active' : '' }}"
            data-hero-dot
            data-slide-index="{{ $loop->index }}"
            aria-label="{{ $heroStatus($loop->iteration) }}"
            aria-controls="hero-slide-{{ $loop->index }}"
            @if ($loop->first) aria-current="true" @endif
          ></button>
        @endforeach
      </div>

      <div class="hero-cinema__transport">
        <button type="button" class="hero-cinema__arrow" data-hero-previous aria-label="{{ $hero['previous_label'] ?? 'Previous slide' }}">
          <svg viewBox="0 0 128 72" aria-hidden="true"><path d="M42 4 10 36l32 32 14-14-18-18 18-18Z" /><path d="M78 4 46 36l32 32 14-14-18-18 18-18Z" /><path d="M114 4 82 36l32 32 14-14-18-18 18-18Z" /></svg>
        </button>
        <button
          type="button"
          class="hero-cinema__playback"
          data-hero-playback
          data-pause-label="{{ $hero['pause_label'] ?? 'Pause slideshow' }}"
          data-play-label="{{ $hero['play_label'] ?? 'Play slideshow' }}"
          aria-label="{{ $hero['pause_label'] ?? 'Pause slideshow' }}"
          aria-pressed="false"
        >
          <svg class="hero-cinema__pause-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h4v14H7zM13 5h4v14h-4z" /></svg>
          <svg class="hero-cinema__play-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7Z" /></svg>
        </button>
        <button type="button" class="hero-cinema__arrow" data-hero-next aria-label="{{ $hero['next_label'] ?? 'Next slide' }}">
          <svg viewBox="0 0 128 72" aria-hidden="true"><path d="m14 4 32 32-32 32L0 54l18-18L0 18Z" /><path d="m50 4 32 32-32 32-14-14 18-18-18-18Z" /><path d="m86 4 32 32-32 32-14-14 18-18-18-18Z" /></svg>
        </button>
      </div>
    </div>
  @endif

  <p class="sr-only" data-hero-live aria-live="polite" aria-atomic="true"></p>
</section>
