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
            <article
              class="hero-cinema__slide{{ $loop->first ? ' is-active' : '' }}"
              data-hero-slide
              data-slide-index="{{ $loop->index }}"
              data-media-type="{{ $slide['type'] }}"
              data-slide-title="{{ $slide['title'] }}"
              role="group"
              aria-roledescription="{{ $hero['slide_roledescription'] ?? 'slide' }}"
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
                    loop
                    playsinline
                    webkit-playsinline
                    preload="none"
                    @if (! empty($slide['poster_url'])) poster="{{ $slide['poster_url'] }}" @endif
                    @if ($loop->first) autoplay @endif
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
                  @if (! empty($slide['eyebrow']))
                    <p class="hero-cinema__eyebrow" data-text-role="label">{{ $slide['eyebrow'] }}</p>
                  @endif

                  @include('home.partials.hero-title', [
                    'headingTag' => $loop->first ? 'h1' : 'h2',
                    'slide' => $slide,
                  ])

                  @if ($slide['show_ppdb_cta'] ?? false)
                    <a
                      href="{{ $slide['ppdb_url'] }}"
                      class="hero-cinema__cta hero-cinema__cta--ppdb"
                      data-hero-ppdb-cta
                      data-text-role="action"
                    >
                      <span data-hero-roll-label>{{ $slide['ppdb_label'] }}</span>
                      <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>
                    </a>
                  @elseif (! empty($slide['description']))
                    <p class="hero-cinema__description" data-text-role="description">{{ $slide['description'] }}</p>
                  @endif

                  @if (! ($slide['show_ppdb_cta'] ?? false) && ! empty($slide['cta']['label']) && ! empty($slide['cta']['href']))
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
        @endif

        <p class="sr-only" data-hero-live aria-live="polite" aria-atomic="true"></p>
      </section>
