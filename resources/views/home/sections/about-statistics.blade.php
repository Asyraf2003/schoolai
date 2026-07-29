@php
  $aboutMedia = $heroSlides->first(
      static fn (array $slide): bool => ($slide['type'] ?? null) === 'video',
  ) ?? $heroSlides->first();
  $aboutMediaRenderType = $aboutMedia['render_type'] ?? 'image';
  $aboutImageFallback = $aboutMediaRenderType === 'video'
      ? ($aboutMedia['poster_url'] ?? ($hero['fallback_image_url'] ?? null))
      : ($aboutMedia['media_url'] ?? ($hero['fallback_image_url'] ?? null));
  $aboutMediaAlt = (string) ($aboutMedia['media_alt'] ?? __('home.about_stats_story.media_alt'));
  $aboutHeadingTitle = trim(
      (string) __('home.about_stats_story.headline_line_one')
      .' '
      .(string) __('home.about_stats_story.headline_line_two')
  );
@endphp

<section class="about-reel" id="tentang" data-about-reel aria-labelledby="about-reel-title">
  <div class="container about-reel__heading-shell">
    @include('home.partials.editorial-section-heading', [
      'title' => $aboutHeadingTitle,
      'description' => __('home.about_stats_story.description'),
      'headingId' => 'about-reel-title',
      'className' => 'about-reel__shared-heading',
      'lineOne' => __('home.about_stats_story.headline_line_one'),
      'lineTwo' => __('home.about_stats_story.headline_line_two'),
    ])
  </div>

  <div class="about-reel__track" data-about-reel-track>
    <div class="about-reel__stage">
      <div class="about-reel__canvas">
        <svg
          class="about-reel__spline"
          viewBox="0 0 1000 1000"
          preserveAspectRatio="none"
          aria-hidden="true"
          focusable="false"
        >
          <defs>
            <linearGradient id="about-reel-rainbow" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="#ff0055" />
              <stop offset="20%" stop-color="#ff7700" />
              <stop offset="40%" stop-color="#ffdd00" />
              <stop offset="60%" stop-color="#00ff88" />
              <stop offset="80%" stop-color="#0099ff" />
              <stop offset="100%" stop-color="#b000ff" />
            </linearGradient>
          </defs>
          <path
            class="about-reel__spline-line"
            d="M -50,80 C 200,80 300,220 300,380 C 300,520 200,680 400,680 C 600,680 650,320 480,320 C 320,320 320,520 500,520 C 680,520 750,700 850,820 C 920,890 980,950 1050,950"
            pathLength="1"
          />
        </svg>

        @if ($aboutMedia && $aboutImageFallback)
          <figure class="about-reel__media">
            <div class="about-reel__media-frame">
              <img
                data-about-reel-image
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
                  <source data-src="{{ $aboutMedia['media_url'] }}" type="{{ $aboutMedia['video_mime_type'] ?? 'video/mp4' }}" />
                </video>
              @endif
              <canvas class="about-reel__warp" data-about-reel-warp aria-hidden="true"></canvas>
            </div>
          </figure>
        @endif
      </div>
    </div>
  </div>
</section>
