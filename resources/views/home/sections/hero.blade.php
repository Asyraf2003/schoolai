      @include('partials.home-hero-copy-layout')

      <section
        class="hero-cinema"
        id="beranda"
        data-hero-slider
        data-hero-mode="{{ $heroSlideCount > 1 ? 'carousel' : 'opening' }}"
        data-autoplay-interval="{{ $hero['autoplay_interval'] ?? 7000 }}"
        data-slide-label="{{ $hero['slide_label'] ?? 'Slide :current / :total' }}"
        aria-label="{{ $hero['section_label'] ?? 'Al Mustaqbal School' }}"
        @if ($heroSlideCount > 1) aria-roledescription="{{ $hero['carousel_roledescription'] ?? 'carousel' }}" @endif
        tabindex="-1"
      >
        <div class="hero-cinema__viewport">
          @foreach ($heroSlides as $slide)
            <article
              class="hero-cinema__slide{{ $loop->first ? ' is-active' : '' }}"
              data-hero-slide
              data-slide-index="{{ $loop->index }}"
              data-media-type="{{ $slide['type'] }}"
              data-slide-title="{{ $slide['title'] }}"
              role="{{ $heroSlideCount > 1 ? 'group' : 'region' }}"
              @if ($heroSlideCount > 1) aria-roledescription="{{ $hero['slide_roledescription'] ?? 'slide' }}" @endif
              aria-label="{{ $heroStatus($loop->iteration) }}"
              aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
              @if (! $loop->first) inert @endif
            >
              <div
                class="hero-cinema__media"
                style="--hero-focal-position: {{ $slide['focal_position'] }}; --hero-overlay-strength: {{ $slide['overlay_strength'] }}"
              >
                @if (($slide['render_type'] ?? 'image') === 'video')
                  <video
                    data-hero-video
                    muted
                    playsinline
                    webkit-playsinline
                    preload="{{ $loop->first ? 'metadata' : 'none' }}"
                    @if (! empty($slide['poster_url'])) poster="{{ $slide['poster_url'] }}" @endif
                    @if ($loop->first) autoplay @endif
                    @if ($heroSlideCount === 1) loop @endif
                    aria-hidden="true"
                    tabindex="-1"
                  >
                    <source
                      @if ($loop->first) src="{{ $slide['media_url'] }}" @else data-src="{{ $slide['media_url'] }}" @endif
                      type="{{ $slide['video_mime_type'] ?? 'video/mp4' }}"
                    />
                  </video>
                @else
                  <img
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
              </div>

              <div class="hero-cinema__content container">
                <div class="hero-cinema__copy">
                  @include('home.partials.hero-title', [
                    'headingTag' => $loop->first ? 'h1' : 'h2',
                    'slide' => $slide,
                  ])
                </div>
              </div>
            </article>
          @endforeach
        </div>

        @if ($heroSlideCount > 1)
          <button
            type="button"
            class="hero-cinema__arrow hero-cinema__arrow--previous"
            data-hero-previous
            aria-label="{{ $hero['previous_label'] ?? 'Previous slide' }}"
          >
            <svg viewBox="0 0 128 72" aria-hidden="true">
              <path d="M42 4 10 36l32 32 14-14-18-18 18-18Z" />
              <path d="M78 4 46 36l32 32 14-14-18-18 18-18Z" />
              <path d="M114 4 82 36l32 32 14-14-18-18 18-18Z" />
            </svg>
          </button>

          <button
            type="button"
            class="hero-cinema__arrow hero-cinema__arrow--next"
            data-hero-next
            aria-label="{{ $hero['next_label'] ?? 'Next slide' }}"
          >
            <svg viewBox="0 0 128 72" aria-hidden="true">
              <path d="m14 4 32 32-32 32L0 54l18-18L0 18Z" />
              <path d="m50 4 32 32-32 32-14-14 18-18-18-18Z" />
              <path d="m86 4 32 32-32 32-14-14 18-18-18-18Z" />
            </svg>
          </button>

          <div class="hero-cinema__rail container" aria-label="{{ $hero['carousel_roledescription'] ?? 'carousel' }}">
            <div class="hero-cinema__progress" aria-hidden="true">
              <span class="hero-cinema__progress-bar" data-hero-progress></span>
            </div>
            <div class="hero-cinema__counter" aria-hidden="true">
              <span class="hero-cinema__counter-current" data-hero-current>01</span>
              <span>/</span>
              <span>{{ str_pad((string) $heroSlideCount, 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="hero-cinema__dots" role="tablist" aria-label="{{ $hero['carousel_roledescription'] ?? 'carousel' }}">
              @foreach ($heroSlides as $slide)
                <button
                  type="button"
                  class="hero-cinema__dot{{ $loop->first ? ' is-active' : '' }}"
                  data-hero-dot
                  role="tab"
                  aria-label="{{ $heroStatus($loop->iteration) }}"
                  aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                  tabindex="{{ $loop->first ? '0' : '-1' }}"
                ></button>
              @endforeach
            </div>
          </div>
        @endif

        <button
          type="button"
          class="hero-cinema__playback"
          data-hero-playback
          data-pause-label="{{ $hero['pause_label'] ?? 'Pause video' }}"
          data-play-label="{{ $hero['play_label'] ?? 'Play video' }}"
          aria-label="{{ $hero['pause_label'] ?? 'Pause video' }}"
          aria-pressed="false"
        >
          <svg class="hero-cinema__pause-icon" viewBox="0 0 16 16" aria-hidden="true">
            <path fill="currentColor" d="M3 2h4v12H3zm6 0h4v12H9z" />
          </svg>
          <svg class="hero-cinema__play-icon" viewBox="0 0 16 16" aria-hidden="true">
            <path fill="currentColor" d="m4 2 10 6-10 6z" />
          </svg>
        </button>

        <button
          type="button"
          class="hero-cinema__audio"
          data-hero-audio
          aria-pressed="false"
          aria-label="Audio"
        >
          <svg class="hero-cinema__audio-glyph" viewBox="0 0 44 20" aria-hidden="true">
            <path class="hero-cinema__audio-line" d="M8 10H36" />
            <path
              class="hero-cinema__audio-snake"
              d="M6 10C10 3 18 3 22 10C26 17 34 17 38 10"
            >
              <animate
                attributeName="d"
                dur="820ms"
                repeatCount="indefinite"
                values="M6 10C10 3 18 3 22 10C26 17 34 17 38 10;M6 10C10 17 18 17 22 10C26 3 34 3 38 10;M6 10C10 3 18 3 22 10C26 17 34 17 38 10"
              />
            </path>
          </svg>
        </button>

        <p class="sr-only" data-hero-live aria-live="polite" aria-atomic="true"></p>
      </section>
