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

        <div class="hero-cinema__ornaments" data-hero-ornaments aria-hidden="true">
          <svg
            class="hero-cinema__ornament hero-cinema__ornament--lattice"
            viewBox="0 0 360 620"
            preserveAspectRatio="xMidYMid slice"
            focusable="false"
          >
            <defs>
              <pattern id="hero-geometric-lattice" width="72" height="72" patternUnits="userSpaceOnUse">
                <path d="M36 2 46 26 70 36 46 46 36 70 26 46 2 36 26 26Z" />
                <path d="M0 0 26 26M72 0 46 26M72 72 46 46M0 72 26 46" />
                <circle cx="36" cy="36" r="17" />
              </pattern>
              <linearGradient id="hero-lattice-fade" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0" stop-color="#fff" stop-opacity="0" />
                <stop offset="0.22" stop-color="#fff" stop-opacity="0.9" />
                <stop offset="0.76" stop-color="#fff" stop-opacity="0.72" />
                <stop offset="1" stop-color="#fff" stop-opacity="0" />
              </linearGradient>
              <mask id="hero-lattice-mask">
                <rect width="360" height="620" fill="url(#hero-lattice-fade)" />
              </mask>
            </defs>
            <rect
              width="360"
              height="620"
              fill="url(#hero-geometric-lattice)"
              mask="url(#hero-lattice-mask)"
            />
          </svg>

          <svg
            class="hero-cinema__ornament hero-cinema__ornament--rosette"
            viewBox="0 0 260 260"
            focusable="false"
          >
            <g fill="none" stroke="currentColor">
              <path d="M130 16 149 73 206 54 187 111 244 130 187 149 206 206 149 187 130 244 111 187 54 206 73 149 16 130 73 111 54 54 111 73Z" />
              <path d="M130 46 154 96 214 100 164 130 214 160 154 164 130 214 106 164 46 160 96 130 46 100 106 96Z" />
              <circle cx="130" cy="130" r="67" />
              <circle cx="130" cy="130" r="31" />
            </g>
          </svg>

          <svg
            class="hero-cinema__ornament hero-cinema__ornament--corner"
            viewBox="0 0 420 240"
            focusable="false"
          >
            <g fill="none" stroke="currentColor">
              <path d="M418 14H242l-36 36h-62l-38 38H42L4 126" />
              <path d="M418 34H252l-36 36h-62l-38 38H52l-38 38" />
              <path d="M418 54H262l-36 36h-62l-38 38H62l-38 38" />
              <path d="m222 50 18 18-18 18-18-18Z" />
              <path d="m122 108 18 18-18 18-18-18Z" />
            </g>
          </svg>
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
