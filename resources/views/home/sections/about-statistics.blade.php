      <!-- ======================= ABOUT + STATISTIK ======================= -->
      @php
        $aboutMedia = $heroSlides->first(
            static fn (array $slide): bool => ($slide['type'] ?? null) === 'video',
        ) ?? $heroSlides->first();
        $aboutMediaRenderType = $aboutMedia['render_type'] ?? 'image';
        $aboutMediaAlt = (string) ($aboutMedia['media_alt'] ?? __('home.about_stats_story.section_label'));
      @endphp

      <section
        class="about-stats-story"
        id="tentang"
        data-about-stats-story
        data-media-kind="{{ $aboutMediaRenderType }}"
        aria-labelledby="about-stats-title"
      >
        <div class="about-stats-story__lead-in" aria-hidden="true">
          <span class="about-stats-story__lead-line"></span>
        </div>

        <div class="about-stats-story__track" data-about-stats-track>
          <div class="about-stats-story__sticky">
            <div class="about-stats-story__stage">
              <div class="about-stats-story__ambient" aria-hidden="true">
                <span class="about-stats-story__aura about-stats-story__aura--sun"></span>
                <span class="about-stats-story__aura about-stats-story__aura--mint"></span>
                <span class="about-stats-story__aura about-stats-story__aura--violet"></span>
                <svg
                  class="about-stats-story__orbit"
                  viewBox="0 0 1200 760"
                  preserveAspectRatio="xMidYMid meet"
                  focusable="false"
                >
                  <ellipse cx="600" cy="380" rx="510" ry="265" />
                  <ellipse cx="600" cy="380" rx="390" ry="330" transform="rotate(-18 600 380)" />
                  <path d="M89 377c170-123 325-183 511-183s340 60 511 183" />
                </svg>
                <span class="about-stats-story__spark about-stats-story__spark--one"></span>
                <span class="about-stats-story__spark about-stats-story__spark--two"></span>
                <span class="about-stats-story__spark about-stats-story__spark--three"></span>
              </div>

              @if ($aboutMedia)
                <figure class="about-stats-story__media" data-about-stats-media>
                  <div class="about-stats-story__screen">
                    @if ($aboutMediaRenderType === 'video')
                      <video
                        data-about-stats-video
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
                    @else
                      <img
                        src="{{ $aboutMedia['media_url'] }}"
                        alt="{{ $aboutMediaAlt }}"
                        width="1920"
                        height="1080"
                        loading="lazy"
                        decoding="async"
                        style="object-position: {{ $aboutMedia['focal_position'] ?? 'center center' }}"
                      />
                    @endif

                    <span class="about-stats-story__media-shade" aria-hidden="true"></span>
                    <span class="about-stats-story__media-glint" aria-hidden="true"></span>
                  </div>

                  <span class="about-stats-story__tv-neck" aria-hidden="true"></span>
                  <span class="about-stats-story__tv-foot" aria-hidden="true"></span>
                  <span class="about-stats-story__tv-shadow" aria-hidden="true"></span>
                  <figcaption class="sr-only">{{ $aboutMediaAlt }}</figcaption>
                </figure>
              @endif

              <article class="about-stats-story__intro" data-about-stats-about>
                <p class="about-stats-story__kicker">
                  <span class="about-stats-story__kicker-line" aria-hidden="true"></span>
                  <span>
                    {{ __('home.about_stats_story.board_title') }}
                  </span>
                </p>

                <h2 class="about-stats-story__title" id="about-stats-title">
                  {{ __('home.about_stats_story.eyebrow') }}
                </h2>

                <p class="about-stats-story__description">
                  {{ __('home.about_stats_story.description') }}
                </p>
              </article>

              <ol
                class="about-stats-story__stats"
                aria-label="{{ __('home.about_stats_story.statistics_label') }}"
              >
                @foreach ($stats as $stat)
                  @php
                    $statValue = trim((string) ($stat['value'] ?? (($stat['count'] ?? '') . ($stat['suffix'] ?? ''))));
                    $statAccents = ['#f4aa00', '#00a47b', '#ff5b2d', '#6f63e9'];
                    $statAccent = $statAccents[$loop->index % count($statAccents)];
                  @endphp

                  <li
                    class="about-stats-story__stat"
                    data-about-stats-item
                    data-stat-slot="{{ $loop->index }}"
                    style="--stat-accent: {{ $statAccent }}"
                  >
                    <article class="about-stats-story__stat-callout">
                      <span class="about-stats-story__stat-index" aria-hidden="true">
                        {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                      </span>
                      <p
                        class="about-stats-story__number"
                        data-about-stats-number
                        data-stat-value="{{ $statValue }}"
                      >{{ $statValue }}</p>
                      <h3
                        class="about-stats-story__label"
                        data-about-stats-label
                      >{{ $stat['label'] }}</h3>
                      <span class="about-stats-story__stat-line" aria-hidden="true"></span>
                    </article>
                  </li>
                @endforeach
              </ol>

              <div class="about-stats-story__progress" aria-hidden="true">
                <span class="about-stats-story__progress-track">
                  <span class="about-stats-story__progress-fill"></span>
                </span>
                <span class="about-stats-story__progress-count">
                  @foreach ($stats as $stat)
                    <i data-about-stats-progress-dot></i>
                  @endforeach
                </span>
              </div>

              <p class="about-stats-story__scroll-hint" aria-hidden="true">
                <span>{{ __('home.about_stats_story.scroll_hint') }}</span>
                <i></i>
              </p>
            </div>
          </div>
        </div>
      </section>
