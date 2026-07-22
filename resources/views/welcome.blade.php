<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @include('partials.site-head-meta', [
      'pageTitle' => $meta['title'],
      'pageDescription' => $meta['description'],
    ])
    @vite([
      'resources/css/pages/welcome.css',
      'resources/css/pages/welcome-hero.css',
      'resources/js/pages/welcome.js',
      'resources/js/pages/welcome-hero.js',
    ])
  </head>
  <body class="home-page nav-shell">
    <!-- Skip link untuk aksesibilitas keyboard -->
    <a href="#main-content" class="skip-link">{{ __('home.accessibility.skip_to_content') }}</a>

    @include('partials.site-navbar', ['navbar' => $navbar, 'siteNavMode' => 'home'])

    <main id="main-content">
      <!-- ======================= HERO ======================= -->
      @php
        $heroSlides = collect($hero['slides'] ?? [])->values();
        $heroSlideCount = $heroSlides->count();
        $heroStatus = static fn (int $current): string => strtr(
            (string) ($hero['slide_label'] ?? 'Slide :current / :total'),
            [
                ':current' => (string) $current,
                ':total' => (string) $heroSlideCount,
            ],
        );
      @endphp

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
                    <p class="hero-cinema__eyebrow">{{ $slide['eyebrow'] }}</p>
                  @endif

                  @if ($loop->first)
                    <h1 class="hero-cinema__title">{{ $slide['title'] }}</h1>
                  @else
                    <h2 class="hero-cinema__title">{{ $slide['title'] }}</h2>
                  @endif

                  @if (! empty($slide['description']))
                    <p class="hero-cinema__description">{{ $slide['description'] }}</p>
                  @endif

                  @if (! empty($slide['cta']['label']) && ! empty($slide['cta']['href']))
                    <a href="{{ $slide['cta']['href'] }}" class="hero-cinema__cta">
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

      <!-- ======================= STATISTIK ======================= -->
      <section class="stats-ribbon reveal">
        <div class="container stats-ribbon__grid">
          @foreach ($stats as $stat)
            @php
              $statValue = trim((string) ($stat['value'] ?? (($stat['count'] ?? '') . ($stat['suffix'] ?? ''))));
              $statCount = $stat['count'] ?? null;
              $statSuffix = $stat['suffix'] ?? '';
              $canAnimateCount = is_numeric($statCount);
            @endphp
            <div class="stat-item">
              <span
                class="stat-item__number"
                @if ($canAnimateCount) data-count="{{ $statCount }}" @endif
                @if ($canAnimateCount && $statSuffix !== '') data-suffix="{{ $statSuffix }}" @endif
              >{{ $canAnimateCount ? '0' : $statValue }}</span>
              <span class="stat-item__label">{{ $stat['label'] }}</span>
            </div>
          @endforeach
        </div>
      </section>

      <!-- ======================= VISI MISI ======================= -->
      <section class="visi-misi section" id="visi-misi">
        <div class="container">
          <header class="section-head section-head--center visi-misi__head reveal">
            <h2 class="section-title">{{ $visiMisi['section_title'] ?? $visiMisi['vision']['title'] }}</h2>
            @if (! empty($visiMisi['section_subtitle']))
              <p class="section-subtitle">{{ $visiMisi['section_subtitle'] }}</p>
            @endif
          </header>

          <div class="visi-misi__shell">
            <article class="visi-card reveal" tabindex="0">
              <div class="visi-card__topline">
                <span class="visi-card__pulse" aria-hidden="true"></span>
              </div>

              <h3 class="visi-card__title">{{ $visiMisi['vision']['title'] }}</h3>

              <p class="visi-card__text">
                @foreach ($visiMisi['vision']['text_parts'] as $part)
                  @if (! empty($part['mark']))
                    <span class="vm-mark vm-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                  @else
                    {{ $part['text'] }}
                  @endif
                @endforeach
              </p>
            </article>

            <div class="misi-panel reveal reveal--delay-1">
              <div class="misi-panel__head">
                <h3>{{ $visiMisi['missions_intro']['title'] }}</h3>
              </div>

              <ol class="misi-list">
                @foreach ($visiMisi['missions'] as $mission)
                  <li class="misi-list__item">
                    <button
                      type="button"
                      class="misi-card{{ $loop->first ? ' is-active' : '' }}"
                      data-mission-card
                      aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                      style="--misi-accent: {{ $mission['accent'] ?? '#0ea5e9' }}"
                    >
                      <span class="misi-card__number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                      <span class="misi-card__body">
                        <span class="misi-card__title">{{ $mission['title'] }}</span>
                        <span class="misi-card__text">
                          @foreach ($mission['text_parts'] as $part)
                            @if (! empty($part['mark']))
                              <span class="vm-mark vm-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                            @else
                              {{ $part['text'] }}
                            @endif
                          @endforeach
                        </span>
                      </span>
                    </button>
                  </li>
                @endforeach
              </ol>
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= NILAI SEKOLAH ======================= -->
      <section class="nilai-section section" id="nilai">
        <div class="container">
          <div class="nilai-section__shell">
            <div class="nilai-section__intro reveal">
              <h2 class="section-title">{{ $schoolValues['title'] }}</h2>
              <p class="section-subtitle">
                {{ $schoolValues['subtitle'] }}
              </p>
            </div>

            <div class="nilai-grid" aria-label="{{ $schoolValues['aria_label'] ?? __('home.nilai_sekolah.aria_label') }}">
              @foreach ($schoolValues['items'] as $value)
                <button
                  type="button"
                  class="nilai-card{{ $loop->first ? ' is-active' : '' }} reveal{{ $loop->index > 0 ? ' reveal--delay-' . $loop->index : '' }}"
                  data-school-value-card
                  aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                  style="--nilai-accent: {{ $value['accent'] ?? '#a855f7' }}"
                >
                  <span class="nilai-card__glow" aria-hidden="true"></span>
                  <span class="nilai-card__top">
                    <span class="nilai-card__code">{{ $value['code'] }}</span>
                    <span class="nilai-card__spark" aria-hidden="true"></span>
                  </span>

                  <span class="nilai-card__content">
                    <span class="nilai-card__title">{{ $value['title'] }}</span>
                    <span class="nilai-card__summary">{{ $value['summary'] }}</span>
                    <span class="nilai-card__description">
                      @foreach ($value['text_parts'] as $part)
                        @if (! empty($part['mark']))
                          <span class="nilai-mark nilai-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                        @else
                          {{ $part['text'] }}
                        @endif
                      @endforeach
                    </span>
                  </span>
                </button>
              @endforeach
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= PROGRAM UNGGULAN ======================= -->
      <section class="program-section section" id="program" aria-labelledby="program-heading">
        <div class="container">
          <header class="section-head section-head--center program-section__head reveal">
            <h2 class="section-title" id="program-heading">{{ $featuredPrograms['section_title'] ?? $featuredPrograms['title'] }}</h2>
            @if (! empty($featuredPrograms['section_subtitle']))
              <p class="section-subtitle">{{ $featuredPrograms['section_subtitle'] }}</p>
            @elseif (! empty($featuredPrograms['subtitle']))
              <p class="section-subtitle">{{ $featuredPrograms['subtitle'] }}</p>
            @endif
          </header>

          <div class="program-section__shell">
            <aside class="program-spotlight reveal">
              <h3 class="program-spotlight__title">{{ $featuredPrograms['title'] }}</h3>
              <p class="program-spotlight__subtitle">
                {{ $featuredPrograms['subtitle'] }}
              </p>

              <div class="program-spotlight__chips" aria-label="{{ $featuredPrograms['chips_aria_label'] ?? __('home.program_unggulan.chips_aria_label') }}">
                @foreach (($featuredPrograms['chips'] ?? []) as $chip)
                  <span>{{ $chip }}</span>
                @endforeach
              </div>
            </aside>

            <div class="program-flow" aria-label="{{ $featuredPrograms['flow_aria_label'] ?? __('home.program_unggulan.flow_aria_label') }}">
              @forelse ($featuredPrograms['items'] as $program)
                <button
                  type="button"
                  class="program-card{{ $loop->first ? ' is-active' : '' }}"
                  data-featured-program-card
                  aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                  style="--program-accent: {{ $program['accent'] ?? '#0ea5e9' }}"
                >
                  <span class="program-card__orb" aria-hidden="true"></span>

                  <span class="program-card__head">
                    <span class="program-card__code">{{ $program['code'] }}</span>
                    <span class="program-card__label">{{ $program['label'] }}</span>
                  </span>

                  <span class="program-card__body">
                    <span class="program-card__title">{{ $program['title'] }}</span>
                    <span class="program-card__summary">{{ $program['summary'] }}</span>
                    <span class="program-card__description">
                      @foreach ($program['text_parts'] as $part)
                        @if (! empty($part['mark']))
                          <span class="program-mark program-mark--{{ $part['mark'] }}">{{ $part['text'] }}</span>
                        @else
                          {{ $part['text'] }}
                        @endif
                      @endforeach
                    </span>
                  </span>

                  <span class="program-card__footer">
                    <span aria-hidden="true">→</span>
                  </span>
                </button>
              @empty
                <p class="program-empty">{{ $featuredPrograms['empty'] }}</p>
              @endforelse
            </div>
          </div>
        </div>
      </section>

      <!-- ======================= GALERI ======================= -->
      <section class="galeri-section section" id="galeri" aria-labelledby="homepage-gallery-heading">
        <div class="container">
          <header class="section-head section-head--center galeri-section__head reveal">
            <h2 class="section-title section-title--white" id="homepage-gallery-heading">
              {{ $gallerySection['section_title'] ?? $gallerySection['title'] }}
            </h2>
            @if (! empty($gallerySection['section_subtitle']))
              <p class="section-subtitle section-subtitle--white">
                {{ $gallerySection['section_subtitle'] }}
              </p>
            @elseif (! empty($gallerySection['subtitle']))
              <p class="section-subtitle section-subtitle--white">
                {{ $gallerySection['subtitle'] }}
              </p>
            @endif
          </header>

          <div
            class="galeri-story"
            id="galeriGrid"
            data-gallery-story
            data-lightbox-label="{{ $gallerySection['lightbox_label'] ?? __('home.galeri.lightbox_label') }}"
            data-close-label="{{ $gallerySection['close_label'] ?? __('home.galeri.close_label') }}"
            data-video-title="{{ $gallerySection['video_title'] ?? __('home.galeri.video_title') }}"
          >
            <div class="galeri-story__copy" aria-label="{{ $gallerySection['aria_label'] ?? __('home.galeri.aria_label') }}">
              @foreach ($gallerySection['items'] as $item)
                <div
                  class="galeri-story-card{{ $loop->first ? ' is-active' : '' }}"
                  tabindex="0"
                  role="button"
                  data-gallery-story-item
                  data-gallery-index="{{ $loop->index }}"
                  data-title="{{ $item['title'] ?? '' }}"
                  data-caption="{{ $item['caption'] ?? '' }}"
                  data-category="{{ $item['category'] ?? '' }}"
                  data-date="{{ $item['date'] ?? '' }}"
                  data-type-label="{{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}"
                  data-media-url="{{ $item['media_url'] ?? '' }}"
                  data-is-video="{{ ! empty($item['is_video']) ? '1' : '0' }}"
                  style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                >
                  <span class="galeri-story-card__number">
                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                  </span>

                  <div class="galeri-story-card__mobile-media">
                    @if (! empty($item['is_video']))
                      @if (! empty($item['thumbnail_url']))
                        <img
                          data-lazy-media
                          data-lazy-src="{{ $item['thumbnail_url'] }}"
                          alt="{{ $item['title'] }}"
                          class="galeri-story-card__mobile-image"
                          loading="lazy"
                          decoding="async"
                        />
                        <span class="galeri-story-card__mobile-play" aria-hidden="true">▶</span>
                      @else
                        <span
                          class="galeri-story-card__mobile-fallback social-video-cover social-video-cover--{{ $item['video_provider'] ?? 'video' }}"
                          aria-hidden="true"
                        >
                          @if (! empty($item['video_provider_logo_url']))
                            <img
                              src="{{ $item['video_provider_logo_url'] }}"
                              alt=""
                              class="social-video-cover__logo"
                              loading="lazy"
                              decoding="async"
                            />
                          @endif

                          <span class="social-video-cover__brand">
                            {{ $item['video_provider_label'] ?? __('pages.common.media_video') }}
                          </span>
                          <span class="social-video-cover__hint">
                            {{ __('pages.common.play_media') }}
                          </span>
                        </span>
                        <span class="galeri-story-card__mobile-play social-video-cover__play" aria-hidden="true">▶</span>
                      @endif
                    @elseif (! empty($item['media_url']))
                      <img
                        data-lazy-media
                        data-lazy-src="{{ $item['media_url'] }}"
                        alt="{{ $item['title'] }}"
                        class="galeri-story-card__mobile-image"
                        loading="lazy"
                        decoding="async"
                      />
                    @else
                      <span class="galeri-story-card__mobile-fallback">
                        {{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}
                      </span>
                    @endif

                    <span class="galeri-story-card__mobile-badge">
                      {{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}
                    </span>
                  </div>

                  <div class="galeri-story-card__content">
                    <div class="galeri-story-card__meta">
                      @if (! empty($item['category']))
                        <span>{{ $item['category'] }}</span>
                      @endif
                      <span>{{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}</span>
                      @if (! empty($item['date']))
                        <span>{{ $item['date'] }}</span>
                      @endif
                    </div>

                    <h3>{{ $item['title'] }}</h3>

                    @if (! empty($item['caption']))
                      <p>{{ $item['caption'] }}</p>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>

            <aside class="galeri-story__visual" aria-label="{{ $gallerySection['visual_aria_label'] ?? __('home.galeri.visual_aria_label') }}">
              <div class="galeri-story-visual__track" data-gallery-visual-track>
                @foreach ($gallerySection['items'] as $item)
                  <div
                    class="galeri-story-visual__panel{{ $loop->first ? ' is-active' : '' }}"
                    data-gallery-visual-panel
                    data-gallery-index="{{ $loop->index }}"
                    role="button"
                    tabindex="0"
                    aria-label="{{ $gallerySection['open_media_prefix'] ?? __('home.galeri.open_media_prefix') }} {{ $item['title'] ?? ($gallerySection['fallback_item_label'] ?? __('home.galeri.fallback_item_label')) }}"
                    style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                  >
                    <div class="galeri-story-visual__media">
                      @if (! empty($item['is_video']))
                        @if (! empty($item['thumbnail_url']))
                          <img
                            data-lazy-media
                            data-lazy-src="{{ $item['thumbnail_url'] }}"
                            alt="{{ $item['title'] }}"
                            class="galeri-story-visual__image"
                            loading="lazy"
                            decoding="async"
                          />
                          <span class="galeri-story-visual__play" aria-hidden="true">▶</span>
                        @else
                          <span
                            class="galeri-story-visual__fallback social-video-cover social-video-cover--{{ $item['video_provider'] ?? 'video' }}"
                            aria-hidden="true"
                          >
                            @if (! empty($item['video_provider_logo_url']))
                              <img
                                src="{{ $item['video_provider_logo_url'] }}"
                                alt=""
                                class="social-video-cover__logo"
                                loading="lazy"
                                decoding="async"
                              />
                            @endif

                            <span class="social-video-cover__brand">
                              {{ $item['video_provider_label'] ?? __('pages.common.media_video') }}
                            </span>
                            <span class="social-video-cover__hint">
                              {{ __('pages.common.play_media') }}
                            </span>
                          </span>
                          <span class="galeri-story-visual__play social-video-cover__play" aria-hidden="true">▶</span>
                        @endif
                      @elseif (! empty($item['media_url']))
                        <img
                          data-lazy-media
                          data-lazy-src="{{ $item['media_url'] }}"
                          alt="{{ $item['title'] }}"
                          class="galeri-story-visual__image"
                          loading="lazy"
                          decoding="async"
                        />
                      @else
                        <span class="galeri-story-visual__fallback">
                          {{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}
                        </span>
                      @endif

                      <span class="galeri-story-visual__badge">
                        {{ $item['type_label'] ?? ($gallerySection['default_type_label'] ?? __('home.galeri.default_type_label')) }}
                      </span>
                    </div>
                  </div>
                @endforeach
              </div>
            </aside>
          </div>

          @if (! empty($gallerySection['cta']['href']) && ! empty($gallerySection['cta']['label']))
            <div class="galeri-section__action">
              <a href="{{ $gallerySection['cta']['href'] }}" class="btn btn--white">
                {{ $gallerySection['cta']['label'] }}
              </a>
            </div>
          @endif
        </div>
      </section>

      <!-- ======================= ARTIKEL ======================= -->
      <section class="artikel-section section artikel-section--digest" id="artikel" aria-labelledby="artikel-heading">
        <div class="container">
          <div class="section-head artikel-section__head">
            <h2 class="section-title" id="artikel-heading">{{ $articlesSection['title'] }}</h2>
            @if (! empty($articlesSection['subtitle']))
              <p class="section-subtitle artikel-section__subtitle">
                {{ $articlesSection['subtitle'] }}
              </p>
            @endif
          </div>

          @php
            $articleItems = array_slice($articlesSection['items'] ?? [], 0, 3);
            $featuredArticle = $articleItems[0] ?? null;
            $digestArticles = array_slice($articleItems, 1);
          @endphp

          @if ($featuredArticle)
            <div class="artikel-digest">
              <article
                class="artikel-digest__hero reveal"
                style="--artikel-g1: {{ $featuredArticle['gradient_from'] ?? 'var(--color-yellow-soft)' }}; --artikel-g2: {{ $featuredArticle['gradient_to'] ?? 'var(--color-orange-soft)' }}"
              >
                <div class="artikel-digest__hero-media">
                  @if (! empty($featuredArticle['thumbnail_url']))
                    <img
                      src="{{ $featuredArticle['thumbnail_url'] }}"
                      alt="{{ $featuredArticle['title'] }}"
                      class="artikel-digest__media-image"
                      loading="lazy"
                      decoding="async"
                    >
                  @else
                    <span class="artikel-digest__emoji" aria-hidden="true">{{ $featuredArticle['emoji'] ?? '📰' }}</span>
                  @endif

                  <span class="artikel-digest__issue" aria-hidden="true">{{ $featuredArticle['issue'] ?? '01' }}</span>
                  <span class="artikel-digest__spark artikel-digest__spark--one" aria-hidden="true"></span>
                  <span class="artikel-digest__spark artikel-digest__spark--two" aria-hidden="true"></span>
                </div>

                <div class="artikel-digest__hero-body">
                  <div class="artikel-digest__meta">
                    @if (! empty($featuredArticle['category']))
                      <span>{{ $featuredArticle['category'] }}</span>
                    @endif
                    <span>{{ $featuredArticle['date'] }}</span>
                    @if (! empty($featuredArticle['reading_time']))
                      <span>{{ $featuredArticle['reading_time'] }}</span>
                    @endif
                  </div>

                  <h3 class="artikel-digest__hero-title">
                    <a href="{{ $featuredArticle['href'] }}">
                      {{ $featuredArticle['title'] }}
                    </a>
                  </h3>

                  <p class="artikel-digest__hero-description">
                    {{ $featuredArticle['description'] }}
                  </p>

                  @if (! empty($featuredArticle['highlight']))
                    <p class="artikel-digest__highlight">
                      {{ $featuredArticle['highlight'] }}
                    </p>
                  @endif
                </div>
              </article>

              <div class="artikel-digest__rail" aria-label="{{ $articlesSection['rail_aria_label'] ?? __('home.artikel.rail_aria_label') }}">
                @forelse ($digestArticles as $article)
                  <article
                    class="artikel-digest-card reveal{{ $loop->index > 0 ? ' reveal--delay-' . min($loop->index, 3) : ' reveal--delay-1' }}"
                    style="--artikel-g1: {{ $article['gradient_from'] ?? 'var(--color-mint-soft)' }}; --artikel-g2: {{ $article['gradient_to'] ?? 'var(--color-blue-soft)' }}"
                  >
                    <a
                      href="{{ $article['href'] }}"
                      class="artikel-digest-card__link"
                      aria-label="{{ $articlesSection['read_more'] }}: {{ $article['title'] }}"
                    >
                      <span class="artikel-digest-card__media" aria-hidden="true">
                        @if (! empty($article['thumbnail_url']))
                          <img
                            src="{{ $article['thumbnail_url'] }}"
                            alt=""
                            class="artikel-digest-card__media-image"
                            loading="lazy"
                            decoding="async"
                          >
                        @else
                          <span class="artikel-digest-card__emoji">{{ $article['emoji'] ?? '📚' }}</span>
                        @endif

                        <span class="artikel-digest-card__issue">{{ $article['issue'] ?? str_pad((string) ($loop->iteration + 1), 2, '0', STR_PAD_LEFT) }}</span>
                      </span>

                      <span class="artikel-digest-card__content">
                        <span class="artikel-digest__meta">
                          @if (! empty($article['category']))
                            <span>{{ $article['category'] }}</span>
                          @endif
                          <span>{{ $article['date'] }}</span>
                          @if (! empty($article['reading_time']))
                            <span>{{ $article['reading_time'] }}</span>
                          @endif
                        </span>

                        <span class="artikel-digest-card__title">
                          {{ $article['title'] }}
                        </span>

                        <span class="artikel-digest-card__description">
                          {{ $article['description'] }}
                        </span>

                        @if (! empty($article['highlight']))
                          <span class="artikel-digest-card__highlight">
                            {{ $article['highlight'] }}
                          </span>
                        @endif
                      </span>
                    </a>
                  </article>
                @empty
                  <p class="artikel-empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
                @endforelse
              </div>
            </div>

            @if (! empty($articlesSection['cta']['href']) && ! empty($articlesSection['cta']['label']))
              <div class="artikel-section__action">
                <a href="{{ $articlesSection['cta']['href'] }}" class="btn btn--primary">
                  {{ $articlesSection['cta']['label'] }}
                </a>
              </div>
            @endif
          @else
            <p class="artikel-empty">{{ $articlesSection['empty'] ?? __('home.artikel.empty') }}</p>
          @endif
        </div>
      </section>

    </main>

    @include('partials.site-footer', ['footerSection' => $footerSection])

  </body>
</html>
