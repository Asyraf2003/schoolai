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
              data-media-type="video"
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
                <video
                  data-hero-video
                  muted
                  loop
                  playsinline
                  webkit-playsinline
                  preload="auto"
                  @if (! empty($slide['poster_url'])) poster="{{ $slide['poster_url'] }}" @endif
                  autoplay
                  aria-hidden="true"
                  tabindex="-1"
                >
                  <source
                    src="{{ $slide['media_url'] }}"
                    type="video/mp4"
                  />
                </video>
              </div>

              <div class="hero-cinema__content container">
                <div class="hero-cinema__copy">
                  @include('home.partials.hero-title', [
                    'headingTag' => 'h1',
                    'slide' => $slide,
                  ])
                </div>
              </div>
            </article>
          @endforeach
        </div>

        <p class="sr-only" data-hero-live aria-live="polite" aria-atomic="true"></p>
      </section>
