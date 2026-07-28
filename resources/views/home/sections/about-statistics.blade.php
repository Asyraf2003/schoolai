@php
  $aboutMedia = $heroSlides->first(
      static fn (array $slide): bool => ($slide['type'] ?? null) === 'video',
  ) ?? $heroSlides->first();
  $aboutMediaRenderType = $aboutMedia['render_type'] ?? 'image';
  $aboutImageFallback = $aboutMediaRenderType === 'video'
      ? ($aboutMedia['poster_url'] ?? ($hero['fallback_image_url'] ?? null))
      : ($aboutMedia['media_url'] ?? ($hero['fallback_image_url'] ?? null));
  $aboutMediaAlt = (string) ($aboutMedia['media_alt'] ?? __('home.about_stats_story.media_alt'));
@endphp

<section
  class="about-reel"
  id="tentang"
  data-about-reel
  data-media-kind="{{ $aboutMediaRenderType }}"
  aria-labelledby="about-reel-title"
>
  <div class="about-reel__track" data-about-reel-track>
    <div class="about-reel__stage">
      <div class="about-reel__canvas">
        <div class="about-reel__editorial">
          <p class="about-reel__eyebrow">{{ __('home.about_stats_story.board_title') }}</p>

          <h2 class="about-reel__headline" id="about-reel-title">
            <span>{{ __('home.about_stats_story.headline_line_one') }}</span>
            <span>{{ __('home.about_stats_story.headline_line_two') }}</span>
          </h2>

          <div class="about-reel__details">
            <p class="about-reel__description">
              {{ __('home.about_stats_story.description') }}
            </p>

            <a class="about-reel__cta" href="#visi-misi">
              <span>{{ __('home.about_stats_story.cta') }}</span>
              <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                <path d="M5 12h13M13 7l5 5-5 5" />
              </svg>
            </a>
          </div>
        </div>

        <svg
          class="about-reel__spline"
          viewBox="0 0 1600 520"
          preserveAspectRatio="none"
          aria-hidden="true"
          focusable="false"
        >
          <defs>
            <linearGradient id="about-reel-rainbow" x1="0" y1="0" x2="1" y2="0">
              <stop offset="0" stop-color="#3979b8" />
              <stop offset=".17" stop-color="#7166ad" />
              <stop offset=".34" stop-color="#c76687" />
              <stop offset=".5" stop-color="#df793f" />
              <stop offset=".66" stop-color="#d9aa35" />
              <stop offset=".83" stop-color="#55a06d" />
              <stop offset="1" stop-color="#3f99a2" />
            </linearGradient>
          </defs>
          <path
            class="about-reel__spline-underlay"
            d="M-50 390C190 70 410 80 570 260s300 300 470-10S1330-20 1660 170"
            pathLength="1"
          />
          <path
            class="about-reel__spline-line"
            d="M-50 390C190 70 410 80 570 260s300 300 470-10S1330-20 1660 170"
            pathLength="1"
          />
        </svg>

        @if ($aboutMedia && $aboutImageFallback)
          <figure class="about-reel__media" data-about-reel-media>
            <figcaption class="about-reel__media-label">
              <span aria-hidden="true"></span>
              {{ __('home.about_stats_story.media_label') }}
            </figcaption>

            <div class="about-reel__media-frame">
              <img
                class="about-reel__media-fallback"
                src="{{ $aboutImageFallback }}"
                alt="{{ $aboutMediaAlt }}"
                width="1920"
                height="1080"
                loading="lazy"
                decoding="async"
                style="object-position: {{ $aboutMedia['focal_position'] ?? 'center center' }}"
              />

              @if ($aboutMediaRenderType === 'video')
                <video
                  class="about-reel__video"
                  data-about-reel-video
                  muted
                  loop
                  playsinline
                  webkit-playsinline
                  preload="none"
                  @if (! empty($aboutMedia['poster_url'])) poster="{{ $aboutMedia['poster_url'] }}" @endif
                  aria-hidden="true"
                  tabindex="-1"
                  style="object-position: {{ $aboutMedia['focal_position'] ?? 'center center' }}"
                >
                  <source
                    data-src="{{ $aboutMedia['media_url'] }}"
                    type="{{ $aboutMedia['video_mime_type'] ?? 'video/mp4' }}"
                  />
                </video>
              @endif
            </div>
          </figure>
        @endif
      </div>
    </div>
  </div>
</section>
