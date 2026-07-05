<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $meta['title'] }}</title>
    <meta name="description" content="{{ $meta['description'] }}" />
    <link
      rel="icon"
      href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.85em%22 font-size=%2290%22>🎨</text></svg>"
    />
    @vite(['resources/css/pages/welcome.css', 'resources/js/pages/welcome.js'])
  </head>
  <body>
    <!-- Skip link untuk aksesibilitas keyboard -->
    <a href="#main-content" class="skip-link">Langsung ke konten utama</a>

    <!-- Definisi SVG yang dipakai berulang (wave divider) agar file tetap ringan -->
    <svg width="0" height="0" style="position: absolute" aria-hidden="true">
      <defs>
        <path
          id="wave-shape"
          d="M0,40 C240,110 480,-20 720,40 C960,100 1200,-10 1440,40 L1440,120 L0,120 Z"
        ></path>
        <path
          id="blob-shape"
          d="M45.6,-58.3C58.4,-49.6,67.4,-33.9,71.6,-16.6C75.8,0.7,75.2,19.7,66.9,34.2C58.6,48.7,42.6,58.7,25.6,64.5C8.6,70.3,-9.4,71.9,-25.8,66.6C-42.2,61.3,-57,49.1,-65.4,33.4C-73.8,17.7,-75.8,-1.5,-70.4,-18.1C-65,-34.7,-52.2,-48.7,-37.3,-57C-22.4,-65.3,-11.2,-67.9,4.4,-74C20,-80.1,45.6,-67.1,45.6,-58.3Z"
        ></path>
      </defs>
    </svg>

    <!-- ======================= NAVBAR ======================= -->
    <header class="navbar" id="navbar">
      <div class="navbar__inner container">
        <a href="{{ $navbar['logo']['href'] }}" class="navbar__logo" aria-label="{{ $navbar['logo']['line_1'] }} {{ $navbar['logo']['line_2'] }}">
          <span class="navbar__logo-icon">
            @if (! empty($navbar['logo']['image_url']))
              <img
                src="{{ $navbar['logo']['image_url'] }}"
                alt="{{ $navbar['logo']['image_alt'] ?? ($navbar['logo']['line_1'] . ' ' . $navbar['logo']['line_2']) }}"
                class="navbar__logo-image"
              />
            @else
              {{ $navbar['logo']['icon'] }}
            @endif
          </span>
          <span class="navbar__logo-text">
            {{ $navbar['logo']['line_1'] }}<br /><small>{{ $navbar['logo']['line_2'] }}</small>
          </span>
        </a>

        <nav class="navbar__menu" id="navMenu" aria-label="{{ $navbar['aria_label'] }}">
          <ul>
            @foreach ($navbar['items'] as $item)
              <li>
                @if (! empty($item['disabled']))
                  <span class="nav-link nav-link--dummy" aria-disabled="true">
                    {{ $item['label'] }}
                    @if (! empty($item['badge']))
                      <small class="nav-link__badge">{{ $item['badge'] }}</small>
                    @endif
                  </span>
                @else
                  <a href="{{ $item['href'] }}" class="nav-link {{ $loop->first ? 'active' : '' }}">
                    {{ $item['label'] }}
                    @if (! empty($item['badge']))
                      <small class="nav-link__badge">{{ $item['badge'] }}</small>
                    @endif
                  </a>
                @endif
              </li>
            @endforeach
          </ul>
          <a href="{{ $navbar['cta']['href'] }}" class="btn btn--primary navbar__cta">
            {{ $navbar['cta']['label'] }}
          </a>
        </nav>

        <button
          class="hamburger"
          id="hamburgerBtn"
          aria-label="{{ $navbar['mobile_open_label'] }}"
          aria-expanded="false"
          aria-controls="navMenu"
        >
          <span></span><span></span><span></span>
        </button>
      </div>
    </header>

    <!-- Overlay gelap saat menu mobile terbuka -->
    <div class="nav-overlay" id="navOverlay"></div>

    <main id="main-content">
      <!-- ======================= HERO ======================= -->
      <section
        class="hero"
        id="beranda"
        @if (! empty($hero['background_image_url']))
          style="--hero-bg-image: url('{{ $hero['background_image_url'] }}')"
        @endif
      >
        <div class="hero__decor" aria-hidden="true">
          <div class="blob blob--yellow blob--1"></div>
          <div class="blob blob--blue blob--2"></div>
        </div>

        <div class="container hero__inner">
          <div class="hero__content reveal">
            <p class="eyebrow eyebrow--pink">
              {{ $hero['eyebrow'] }}
            </p>

            <h1 class="hero__title">
              {{ $hero['title_before'] }}
              <span class="text-highlight">{{ $hero['title_highlight'] }}</span>
              {{ $hero['title_after'] }}
            </h1>

            <p class="hero__subtitle">
              {{ $hero['subtitle'] }}
            </p>

            <div class="hero__actions">
              <a href="{{ $hero['primary_cta']['href'] }}" class="btn btn--primary btn--lg">
                {{ $hero['primary_cta']['label'] }}
              </a>

              <a href="{{ $hero['secondary_cta']['href'] }}" class="btn btn--ghost btn--lg">
                {{ $hero['secondary_cta']['label'] }}
              </a>
            </div>
          </div>

          <div class="hero__visual reveal reveal--delay-1">
            <div class="hero__illustration" id="tiltIllustration">
              <div class="illustration-card">
                @if (! empty($hero['visual_image_url']))
                  <div class="hero-photo-stack" aria-label="{{ $hero['visual_image_alt'] ?? 'Foto lingkungan sekolah' }}">
                    <img
                      src="{{ $hero['visual_image_url'] }}"
                      alt="{{ $hero['visual_image_alt'] ?? 'Foto lingkungan sekolah' }}"
                      class="hero-photo-frame hero-photo-frame--main"
                    />

                    <img
                      src="{{ $hero['visual_image_url'] }}"
                      alt=""
                      class="hero-photo-frame hero-photo-frame--top"
                      aria-hidden="true"
                    />

                    @if (! empty($hero['logo_image_url']))
                      <img
                        src="{{ $hero['logo_image_url'] }}"
                        alt="{{ $hero['logo_image_alt'] ?? 'Al Mustaqbal Islamic School' }}"
                        class="hero-photo-logo"
                      />
                    @endif
                  </div>
                @else
                  <div class="hero-photo-fallback">
                    Foto sekolah belum tersedia
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="hero-wave-divider" aria-hidden="true">
          <span class="hero-wave hero-wave--1"></span>
          <span class="hero-wave hero-wave--2"></span>
          <span class="hero-wave hero-wave--3"></span>
          <span class="hero-wave hero-wave--4"></span>
        </div>
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
          <div class="visi-misi__shell">
            <article class="visi-card reveal" tabindex="0">
              <div class="visi-card__topline">
                <span class="visi-card__kicker">{{ $visiMisi['vision']['eyebrow'] }}</span>
                <span class="visi-card__pulse" aria-hidden="true"></span>
              </div>

              <h2 class="visi-card__title">{{ $visiMisi['vision']['title'] }}</h2>

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
                <span class="misi-panel__kicker">{{ $visiMisi['missions_intro']['eyebrow'] }}</span>
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

            <div class="nilai-grid" aria-label="Nilai sekolah Al-Mustaqbal">
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
      <section class="program-section section" id="program">
        <div class="container">
          <div class="program-section__shell">
            <aside class="program-spotlight reveal">
              <span class="program-spotlight__kicker">{{ $featuredPrograms['eyebrow'] }}</span>
              <h2 class="program-spotlight__title">{{ $featuredPrograms['title'] }}</h2>
              <p class="program-spotlight__subtitle">
                {{ $featuredPrograms['subtitle'] }}
              </p>

              <div class="program-spotlight__chips" aria-label="Ringkasan program">
                <span>6 Program</span>
                <span>TK & SD</span>
                <span>Qur’ani</span>
              </div>
            </aside>

            <div class="program-flow" aria-label="Daftar program unggulan">
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
                    <span>Lihat fokus</span>
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
      <section class="galeri-section section" id="galeri">
        <div class="container">
          <div class="section-head">
            <h2 class="section-title section-title--white">{{ $gallerySection['title'] }}</h2>
            <p class="section-subtitle section-subtitle--white">
              {{ $gallerySection['subtitle'] }}
            </p>
          </div>

          @php
            $initialGalleryItem = $gallerySection['items'][0] ?? null;
          @endphp

          <div class="galeri-story" id="galeriGrid" data-gallery-story>
            <div class="galeri-story__copy" aria-label="Daftar momen galeri terbaru">
              @foreach ($gallerySection['items'] as $item)
                <article
                  class="galeri-story-card{{ $loop->first ? ' is-active' : '' }}"
                  tabindex="0"
                  data-gallery-story-item
                  data-gallery-index="{{ $loop->index }}"
                  data-title="{{ $item['title'] ?? '' }}"
                  data-caption="{{ $item['caption'] ?? '' }}"
                  data-category="{{ $item['category'] ?? '' }}"
                  data-date="{{ $item['date'] ?? '' }}"
                  data-type-label="{{ $item['type_label'] ?? 'Foto' }}"
                  data-thumbnail-url="{{ $item['thumbnail_url'] ?? '' }}"
                  data-fallback-icon="{{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}"
                  data-instagram-url="{{ $item['instagram_url'] ?? '' }}"
                  data-is-video="{{ ! empty($item['is_video']) ? '1' : '0' }}"
                  style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                >
                  <span class="galeri-story-card__number">
                    {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                  </span>

                  <div class="galeri-story-card__mobile-media">
                    @if (! empty($item['thumbnail_url']))
                      <img
                        src="{{ $item['thumbnail_url'] }}"
                        alt="{{ $item['title'] }}"
                        class="galeri-story-card__mobile-image"
                        loading="lazy"
                      />
                    @else
                      <span class="galeri-story-card__mobile-fallback">
                        {{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}
                      </span>
                    @endif

                    <span class="galeri-story-card__mobile-badge">
                      {{ $item['type_label'] ?? 'Foto' }}
                    </span>

                    @if (! empty($item['is_video']))
                      <span class="galeri-story-card__mobile-play" aria-hidden="true">▶</span>
                    @endif
                  </div>

                  <div class="galeri-story-card__content">
                    <div class="galeri-story-card__meta">
                      @if (! empty($item['category']))
                        <span>{{ $item['category'] }}</span>
                      @endif
                      <span>{{ $item['type_label'] ?? 'Foto' }}</span>
                      @if (! empty($item['date']))
                        <span>{{ $item['date'] }}</span>
                      @endif
                    </div>

                    <h3>{{ $item['title'] }}</h3>

                    @if (! empty($item['caption']))
                      <p>{{ $item['caption'] }}</p>
                    @endif
                  </div>
                </article>
              @endforeach
            </div>

            <aside class="galeri-story__visual" aria-hidden="true">
              <div class="galeri-story-visual__track" data-gallery-visual-track>
                @foreach ($gallerySection['items'] as $item)
                  <div
                    class="galeri-story-visual__panel{{ $loop->first ? ' is-active' : '' }}"
                    data-gallery-visual-panel
                    data-gallery-index="{{ $loop->index }}"
                    style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                  >
                    <div class="galeri-story-visual__media">
                      @if (! empty($item['thumbnail_url']))
                        <img
                          src="{{ $item['thumbnail_url'] }}"
                          alt="{{ $item['title'] }}"
                          class="galeri-story-visual__image"
                          loading="lazy"
                        />
                      @else
                        <span class="galeri-story-visual__fallback">
                          {{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}
                        </span>
                      @endif

                      <span class="galeri-story-visual__badge">
                        {{ $item['type_label'] ?? 'Foto' }}
                      </span>

                      @if (! empty($item['is_video']))
                        <span class="galeri-story-visual__play" aria-hidden="true">▶</span>
                      @endif
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
            $articleItems = $articlesSection['items'] ?? [];
            $featuredArticle = $articleItems[0] ?? null;
            $digestArticles = array_slice($articleItems, 1);
          @endphp

          @if ($featuredArticle)
            <div class="artikel-digest">
              <article
                class="artikel-digest__hero reveal"
                style="--artikel-g1: {{ $featuredArticle['gradient_from'] ?? 'var(--color-yellow-soft)' }}; --artikel-g2: {{ $featuredArticle['gradient_to'] ?? 'var(--color-orange-soft)' }}"
              >
                <div class="artikel-digest__hero-media" aria-hidden="true">
                  <span class="artikel-digest__issue">{{ $featuredArticle['issue'] ?? '01' }}</span>
                  <span class="artikel-digest__emoji">{{ $featuredArticle['emoji'] ?? '📰' }}</span>
                  <span class="artikel-digest__spark artikel-digest__spark--one"></span>
                  <span class="artikel-digest__spark artikel-digest__spark--two"></span>
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

                  <a
                    href="{{ $featuredArticle['href'] }}"
                    class="artikel-digest__cta"
                    aria-label="{{ $articlesSection['read_more'] }}: {{ $featuredArticle['title'] }}"
                  >
                    {{ $articlesSection['read_more'] }}
                    <span aria-hidden="true">→</span>
                  </a>
                </div>
              </article>

              <div class="artikel-digest__rail" aria-label="Cerita sekolah lainnya">
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
                        <span class="artikel-digest-card__issue">{{ $article['issue'] ?? str_pad((string) ($loop->iteration + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="artikel-digest-card__emoji">{{ $article['emoji'] ?? '📚' }}</span>
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
                  <p class="artikel-empty">{{ $articlesSection['empty'] ?? 'Belum ada artikel terbaru.' }}</p>
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
            <p class="artikel-empty">{{ $articlesSection['empty'] ?? 'Belum ada artikel terbaru.' }}</p>
          @endif
        </div>
      </section>

    </main>

    <!-- ======================= FOOTER ======================= -->
    <footer class="site-footer" id="kontak">
      <div class="container site-footer__grid">
        <div class="footer-brand">
          <a href="{{ $footerSection['brand']['href'] }}" class="footer-brand__logo" aria-label="{{ $footerSection['brand']['name'] }}">
            <img
              src="{{ $footerSection['brand']['image'] }}"
              alt="{{ $footerSection['brand']['image_alt'] }}"
              class="footer-brand__logo-image"
              loading="lazy"
              decoding="async"
            />
          </a>

          <div class="footer-location-line">
            <strong>{{ $footerSection['location_title'] }}</strong>
            <a
              href="{{ $footerSection['location_href'] }}"
              target="_blank"
              rel="noopener noreferrer"
            >
              {{ $footerSection['location_label'] }}
            </a>
            @if (! empty($footerSection['location_hint']))
              <p>{{ $footerSection['location_hint'] }}</p>
            @endif
          </div>

          @if (! empty($footerSection['channels']))
            <div class="footer-channel-group" aria-label="{{ $footerSection['channels_title'] }}">
              <strong>{{ $footerSection['channels_title'] }}</strong>

              <div class="footer-channel-grid">
                @foreach ($footerSection['channels'] as $channel)
                  @php
                    $isDisabled = ! empty($channel['disabled']);
                  @endphp

                  @if ($isDisabled)
                    <span class="footer-channel footer-channel--{{ $channel['icon'] }} footer-channel--disabled" aria-disabled="true">
                  @else
                    <a
                      href="{{ $channel['href'] }}"
                      class="footer-channel footer-channel--{{ $channel['icon'] }}"
                      aria-label="{{ $channel['label'] }}"
                      target="{{ str_starts_with($channel['href'], 'http') ? '_blank' : '_self' }}"
                      rel="{{ str_starts_with($channel['href'], 'http') ? 'noopener noreferrer' : '' }}"
                    >
                  @endif
                    @if (! empty($channel['asset']))
                      <img
                        src="{{ $channel['asset'] }}"
                        alt="{{ $channel['asset_alt'] ?? $channel['label'] }}"
                        class="footer-channel__asset"
                        loading="lazy"
                        decoding="async"
                        fetchpriority="low"
                        width="28"
                        height="28"
                      />
                    @else
                      <span class="footer-channel__fallback">{{ substr($channel['label'], 0, 2) }}</span>
                    @endif

                    <span class="footer-channel__body">
                      <span class="footer-channel__label">{{ $channel['label'] }}</span>
                      <small class="footer-channel__note">{{ $channel['note'] }}</small>
                    </span>
                  @if ($isDisabled)
                    </span>
                  @else
                    </a>
                  @endif
                @endforeach
              </div>
            </div>
          @endif
        </div>

        <nav class="footer-links" aria-label="{{ $footerSection['links_title'] }}">
          <h4>{{ $footerSection['links_title'] }}</h4>
          <ul>
            @foreach ($footerSection['links'] as $link)
              <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
          </ul>
        </nav>

        <nav class="footer-gallery-links" aria-label="{{ $footerSection['gallery_links_title'] }}">
          <h4>{{ $footerSection['gallery_links_title'] }}</h4>
          <ul>
            @foreach ($footerSection['gallery_links'] as $link)
              <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
          </ul>
        </nav>

        <div class="footer-partners" aria-labelledby="footer-partners-heading">
          <h4 id="footer-partners-heading">{{ $footerSection['partners_title'] }}</h4>

          <div class="footer-partners__grid">
            @foreach ($footerSection['partners'] as $partner)
              <a
                href="{{ $partner['href'] }}"
                class="footer-partner-card"
                aria-label="{{ $partner['label'] }}"
                target="_blank"
                rel="noopener noreferrer"
              >
                <img
                  src="{{ $partner['image'] }}"
                  alt="{{ $partner['label'] }}"
                  class="footer-partner-card__logo"
                  loading="lazy"
                  decoding="async"
                />
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <div class="site-footer__bottom">
        <p>
          &copy; <span id="currentYear"></span> {{ $footerSection['copyright'] }}
        </p>
      </div>
    </footer>

    <!-- Lightbox galeri -->
    <div class="lightbox" id="lightbox" hidden>
      <div class="lightbox__backdrop" id="lightboxBackdrop"></div>
      <div
        class="lightbox__content"
        role="dialog"
        aria-modal="true"
        aria-label="Pratinjau galeri"
      >
        <button class="lightbox__close" id="lightboxClose" aria-label="Tutup">
          ✕
        </button>
        <div class="lightbox__visual" id="lightboxVisual"></div>
        <p class="lightbox__caption" id="lightboxCaption"></p>
      </div>
    </div>
  </body>
</html>
