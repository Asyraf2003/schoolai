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
                <a href="{{ $item['href'] }}" class="nav-link {{ $loop->first ? 'active' : '' }}">
                  {{ $item['label'] }}
                </a>
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

      <!-- ======================= EKSTRAKURIKULER ======================= -->
      <section class="ekskul-section section" id="ekstrakurikuler">
        <div class="container">
          <div class="ekskul-section__head reveal">
            <div>
              <h2 class="section-title">{{ $extracurricular['title'] }}</h2>
              <p class="section-subtitle">
                {{ $extracurricular['subtitle'] }}
              </p>
            </div>

            <div class="ekskul-section__badge" aria-hidden="true">
              <span>{{ count($extracurricular['items']) }}</span>
              <small>Kegiatan</small>
            </div>
          </div>

          <div
            class="filter-bar ekskul-filter-bar reveal reveal--delay-1"
            role="group"
            aria-label="Filter ekstrakurikuler"
          >
            @foreach ($extracurricular['filters'] as $filter)
              <button
                class="filter-btn {{ $loop->first ? 'active' : '' }}"
                data-filter="{{ $filter['value'] }}"
              >
                {{ $filter['label'] }}
              </button>
            @endforeach
          </div>

          <div class="ekskul-grid" id="ekskulGrid">
            @forelse ($extracurricular['items'] as $item)
              <button
                type="button"
                class="ekskul-card ekskul-card--{{ $item['variant'] ?? 'normal' }} reveal{{ $loop->index > 0 ? ' reveal--delay-' . min($loop->index, 3) : '' }}{{ $loop->first ? ' is-active' : '' }}"
                data-category="{{ $item['category'] }}"
                data-ekskul-card
                aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                style="--ekskul-accent: {{ $item['accent'] ?? '#0ea5e9' }}"
              >
                <span class="ekskul-card__shine" aria-hidden="true"></span>

                <span class="ekskul-card__top">
                  <span class="ekskul-card__icon">{{ $item['icon'] }}</span>
                  <span class="ekskul-card__code">{{ $item['code'] }}</span>
                </span>

                <span class="ekskul-card__body">
                  <span class="ekskul-card__title">{{ $item['title'] }}</span>
                  <span class="ekskul-card__description">{{ $item['description'] }}</span>
                </span>

                <span class="ekskul-card__meta">
                  <span class="tag {{ $item['tag_class'] }}">{{ $item['category_label'] }}</span>
                  <span class="ekskul-card__level">{{ $item['level'] }}</span>
                </span>
              </button>
            @empty
              <p class="text-sm">{{ $extracurricular['empty'] }}</p>
            @endforelse
          </div>

          <p class="ekskul-empty" id="ekskulEmpty" hidden>
            {{ $extracurricular['empty'] }}
          </p>
        </div>
      </section>

      <!-- ======================= GALERI ======================= -->
      <section class="galeri-section section" id="galeri">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--white">{{ $gallerySection['eyebrow'] }}</p>
            <h2 class="section-title section-title--white">{{ $gallerySection['title'] }}</h2>
            <p class="section-subtitle section-subtitle--white">
              {{ $gallerySection['subtitle'] }}
            </p>
          </div>

          <div class="galeri-grid galeri-grid--teaser" id="galeriGrid">
            @foreach ($gallerySection['items'] as $item)
              <article
                class="galeri-item galeri-card galeri-card--{{ $item['variant'] ?? 'normal' }} {{ $item['item_class'] ?? '' }}{{ ! empty($item['is_video']) ? ' galeri-card--video' : '' }}"
                style="--g1: {{ $item['g1'] ?? 'var(--color-orange)' }}; --g2: {{ $item['g2'] ?? 'var(--color-yellow)' }}; --gallery-accent: {{ $item['accent'] ?? '#f97316' }}"
                data-caption="{{ $item['caption'] ?? '' }}"
              >
                <div class="galeri-card__media">
                  @if (! empty($item['thumbnail_url']))
                    <img
                      src="{{ $item['thumbnail_url'] }}"
                      alt="{{ $item['title'] }}"
                      class="galeri-card__image"
                      loading="lazy"
                    />
                  @else
                    <span class="galeri-item__emoji galeri-card__fallback">{{ $item['fallback_icon'] ?? ($item['emoji'] ?? '📸') }}</span>
                  @endif

                  <span class="galeri-card__badge">
                    {{ $item['type_label'] ?? 'Foto' }}
                  </span>

                  @if (! empty($item['is_video']))
                    <span class="galeri-card__play" aria-hidden="true">▶</span>
                  @endif
                </div>

                <div class="galeri-card__body">
                  <div class="galeri-card__meta">
                    @if (! empty($item['category']))
                      <span>{{ $item['category'] }}</span>
                    @endif
                    @if (! empty($item['date']))
                      <span>{{ $item['date'] }}</span>
                    @endif
                  </div>

                  <h3 class="galeri-item__caption galeri-card__title">{{ $item['title'] }}</h3>

                  @if (! empty($item['caption']))
                    <p class="galeri-card__caption">{{ $item['caption'] }}</p>
                  @endif

                  @if (! empty($item['instagram_url']))
                    <a
                      href="{{ $item['instagram_url'] }}"
                      class="galeri-card__link"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      Lihat di Instagram →
                    </a>
                  @else
                    <span class="galeri-card__link galeri-card__link--disabled">
                      Link Instagram belum tersedia
                    </span>
                  @endif
                </div>
              </article>
            @endforeach
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
      <section class="artikel-section section" id="artikel">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--yellow">{{ $articlesSection['eyebrow'] }}</p>
            <h2 class="section-title">{{ $articlesSection['title'] }}</h2>
          </div>
          <div class="artikel-grid">
            @foreach ($articlesSection['items'] as $article)
              <article class="artikel-card reveal{{ $loop->index > 0 ? ' reveal--delay-' . $loop->index : '' }}">
                <div
                  class="artikel-card__thumb"
                  style="
                    background: linear-gradient(
                      135deg,
                      {{ $article['gradient_from'] }},
                      {{ $article['gradient_to'] }}
                    );
                  "
                >
                  {{ $article['emoji'] }}
                </div>
                <div class="artikel-card__body">
                  <span class="artikel-card__date">{{ $article['date'] }}</span>
                  <h3>{{ $article['title'] }}</h3>
                  <p>
                    {{ $article['description'] }}
                  </p>
                  <a href="{{ $article['href'] }}" class="link-arrow">
                    {{ $articlesSection['read_more'] }} →
                  </a>
                </div>
              </article>
            @endforeach
          </div>
        </div>
      </section>

      <!-- ======================= PENGUMUMAN ======================= -->
      <section class="pengumuman-section section" id="pengumuman">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--blue">{{ $announcementsSection['eyebrow'] }}</p>
            <h2 class="section-title">{{ $announcementsSection['title'] }}</h2>
          </div>
          <div class="pengumuman-grid">
            @forelse ($announcementsSection['items'] as $announcement)
              <div class="sticky-note {{ $announcement['note_class'] }} reveal{{ $loop->index > 0 ? ' reveal--delay-' . $loop->index : '' }}">
                <span class="sticky-note__pin">{{ $announcement['pin'] }}</span>
                <span class="sticky-note__date">{{ $announcement['date'] }}</span>
                <h3>{{ $announcement['title'] }}</h3>
                <p>{{ $announcement['description'] }}</p>
              </div>
            @empty
              <p>{{ $announcementsSection['empty'] }}</p>
            @endforelse
          </div>
        </div>
      </section>

      <!-- ======================= FASILITAS ======================= -->
      <section class="fasilitas-section section">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--mint">{{ $facilitiesSection['eyebrow'] }}</p>
            <h2 class="section-title">{{ $facilitiesSection['title'] }}</h2>
          </div>
          <div class="fasilitas-grid">
            @foreach ($facilitiesSection['items'] as $facility)
              <div class="fasilitas-item reveal">
                <span class="fasilitas-item__icon">{{ $facility['icon'] }}</span>
                <p>{{ $facility['label'] }}</p>
              </div>
            @endforeach
          </div>
        </div>
        <div class="wave-divider" aria-hidden="true">
          <svg viewBox="0 0 1440 120" preserveAspectRatio="none">
            <use href="#wave-shape" class="wave-fill-purple"></use>
          </svg>
        </div>
      </section>

      <!-- ======================= KONTAK ======================= -->
      <section class="kontak-section section" id="kontak">
        <div class="container">
          <div class="section-head">
            <p class="eyebrow eyebrow--purple">{{ $contactSection['eyebrow'] }}</p>
            <h2 class="section-title">{{ $contactSection['title'] }}</h2>
          </div>
          <div class="kontak-grid">
            <div class="kontak-info reveal">
              @foreach ($contactSection['items'] as $item)
                <div class="kontak-info__item">
                  <span class="kontak-info__icon">{{ $item['icon'] }}</span>
                  <div>
                    <h4>{{ $item['label'] }}</h4>
                    <p>{{ $item['value'] }}</p>
                  </div>
                </div>
              @endforeach
              <a href="{{ $contactSection['cta']['href'] }}" class="btn btn--primary">
                {{ $contactSection['cta']['label'] }}
              </a>
            </div>

            <!-- Placeholder peta custom dengan CSS, tanpa embed eksternal -->
            <div
              class="map-placeholder reveal reveal--delay-1"
              role="img"
              aria-label="{{ $contactSection['map']['aria_label'] }}"
            >
              <div class="map-placeholder__grid"></div>
              <div class="map-placeholder__road map-placeholder__road--h"></div>
              <div class="map-placeholder__road map-placeholder__road--v"></div>
              <div class="map-placeholder__pin">
                📍<span>{{ $contactSection['map']['pin_label'] }}</span>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- ======================= FOOTER ======================= -->
    <footer class="site-footer">
      <div class="container site-footer__grid">
        <div class="footer-brand">
          <a href="{{ $footerSection['brand']['href'] }}" class="navbar__logo navbar__logo--footer">
            <span class="navbar__logo-icon">{{ $footerSection['brand']['icon'] }}</span>
            <span class="navbar__logo-text"
              >{{ $footerSection['brand']['line_1'] }}<br /><small>{{ $footerSection['brand']['line_2'] }}</small></span
            >
          </a>
          <p>
            {{ $footerSection['brand']['description'] }}
          </p>
          <div class="footer-social">
            @foreach ($footerSection['socials'] as $social)
              <a href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}">{{ $social['icon'] }}</a>
            @endforeach
          </div>
        </div>

        <div class="footer-links">
          <h4>{{ $footerSection['links_title'] }}</h4>
          <ul>
            @foreach ($footerSection['links'] as $link)
              <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
          </ul>
        </div>

        <div class="footer-contact">
          <h4>{{ $footerSection['contact_title'] }}</h4>
          @foreach ($footerSection['contact'] as $contact)
            <p>{{ $contact['icon'] }} {{ $contact['text'] }}</p>
          @endforeach
        </div>
      </div>
      <div class="site-footer__bottom">
        <p>
          &copy; <span id="currentYear"></span> {{ $footerSection['copyright'] }}
        </p>
      </div>
    </footer>

    <!-- Tombol scroll to top -->
    <button
      class="scroll-top-btn"
      id="scrollTopBtn"
      aria-label="Kembali ke atas"
      hidden
    >
      ⬆️
    </button>

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
