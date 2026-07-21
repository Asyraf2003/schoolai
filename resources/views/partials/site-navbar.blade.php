@php
  $siteNavbar = $siteNavbar ?? ($navbar ?? __('home.navbar'));
  $siteNavbar = is_array($siteNavbar) ? $siteNavbar : [];

  $siteNavMode = $siteNavMode ?? (request()->routeIs('home') ? 'home' : 'public');
  $isHomeNav = $siteNavMode === 'home';
  $currentLocale = app()->getLocale();

  $languageItem = collect($siteNavbar['items'] ?? [])
    ->first(fn ($item) => ($item['type'] ?? null) === 'language');

  if (! is_array($languageItem)) {
      $languageItem = [
          'label' => __('pages.common.nav.language'),
          'type' => 'language',
          'options' => [],
      ];
  }

  $languageLabels = match ($currentLocale) {
      'ar' => [
          'id' => 'الإندونيسية',
          'en' => 'الإنجليزية',
          'ar' => 'العربية',
      ],
      'en' => [
          'id' => 'Indonesian',
          'en' => 'English',
          'ar' => 'Arabic',
      ],
      default => [
          'id' => 'Indonesia',
          'en' => 'English',
          'ar' => 'Arab',
      ],
  };

  $languageMobileTitle = match ($currentLocale) {
      'ar' => 'اختر اللغة',
      'en' => 'Choose language',
      default => 'Pilih bahasa',
  };

  $languageMobileActive = match ($currentLocale) {
      'ar' => 'اللغة الحالية',
      'en' => 'Current language',
      default => 'Bahasa aktif',
  };

  $languageItem['options'] = [
      ['locale' => 'id', 'label' => $languageLabels['id'], 'short' => 'ID'],
      ['locale' => 'en', 'label' => $languageLabels['en'], 'short' => 'EN'],
      ['locale' => 'ar', 'label' => $languageLabels['ar'], 'short' => 'AR'],
  ];

  $homeUrl = route('home');
  $contactUrl = '#kontak';

  if ($isHomeNav) {
      $menuItems = $siteNavbar['items'] ?? [];

      $languageItemIndex = collect($menuItems)
          ->search(fn ($item) => ($item['type'] ?? null) === 'language');

      if ($languageItemIndex !== false) {
          $menuItems[$languageItemIndex] = array_replace($menuItems[$languageItemIndex], $languageItem);
      } else {
          $menuItems[] = $languageItem;
      }
  } else {
      $menuItems = [
          [
              'label' => __('pages.common.nav.home'),
              'href' => $homeUrl,
              'route_patterns' => ['home'],
          ]
      ];

      if (! request()->routeIs('artikel', 'artikel.detail')) {
          $menuItems[] = [
              'label' => __('pages.common.nav.artikel'),
              'href' => route('artikel'),
              'route_patterns' => ['artikel', 'artikel.detail'],
          ];
      }

      if (! request()->routeIs('ppdb')) {
          $menuItems[] = [
              'label' => __('pages.common.nav.ppdb'),
              'href' => route('ppdb'),
              'route_patterns' => ['ppdb'],
          ];
      }

      if (! request()->routeIs('galeri')) {
          $menuItems[] = [
              'label' => __('pages.common.nav.galeri'),
              'href' => route('galeri'),
              'route_patterns' => ['galeri'],
          ];
      }

      $menuItems[] = [
          'label' => __('pages.common.nav.contact'),
          'href' => $contactUrl,
      ];

      $menuItems[] = $languageItem;
  }

  $logo = $siteNavbar['logo'] ?? [];
  $logoImageUrl = $logo['image_url'] ?? null;

  if (empty($logoImageUrl) && ! empty($logo['image'])) {
      $logoImageUrl = asset(ltrim((string) $logo['image'], '/'));
  }

  $logoHref = $isHomeNav ? ($logo['href'] ?? '#beranda') : $homeUrl;
  $logoLabel = trim((string) (($logo['line_1'] ?? __('pages.common.school_name')) . ' ' . ($logo['line_2'] ?? '')));
  $showCta = $isHomeNav && ! empty($siteNavbar['cta']);
@endphp

<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  .nav-language__mobile-summary,
  .nav-language__check {
    display: none;
  }

  @media (max-width: 767px) {
    .navbar__menu .nav-language {
      width: 100%;
      margin-top: 10px;
      padding-top: 16px;
      border-top: 1px solid rgba(51, 49, 77, 0.1);
    }

    .navbar__menu .nav-language__button {
      display: none;
    }

    .navbar__menu .nav-language__mobile-summary {
      display: flex;
      align-items: flex-end;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 10px;
    }

    .nav-language__mobile-title {
      display: block;
      color: var(--color-ink);
      font-size: 0.86rem;
      font-weight: 800;
      line-height: 1.2;
    }

    .nav-language__mobile-current {
      display: block;
      margin-top: 3px;
      color: var(--color-ink-soft);
      font-size: 0.72rem;
      font-weight: 600;
    }

    .nav-language__mobile-badge {
      flex: 0 0 auto;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 40px;
      height: 30px;
      padding-inline: 10px;
      border: 1px solid rgba(194, 94, 30, 0.18);
      border-radius: 999px;
      background: var(--color-orange-soft);
      color: #9e3f0d;
      font-size: 0.72rem;
      font-weight: 800;
      letter-spacing: 0.04em;
    }

    .navbar__menu .nav-language__panel,
    .navbar__menu .nav-language.is-open .nav-language__panel {
      position: static !important;
      inset: auto !important;
      display: grid !important;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 8px;
      width: 100% !important;
      min-width: 0 !important;
      margin: 0 !important;
      padding: 0 !important;
      border: 0 !important;
      background: transparent !important;
      box-shadow: none !important;
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: auto !important;
      transform: none !important;
    }

    .navbar__menu .nav-language__form {
      min-width: 0;
      margin: 0;
    }

    .navbar__menu .nav-language__option {
      position: relative;
      width: 100%;
      min-height: 76px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 5px;
      padding: 10px 6px;
      overflow: hidden;
      border: 1px solid rgba(51, 49, 77, 0.1);
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.76);
      color: var(--color-ink);
      box-shadow: 0 5px 14px rgba(51, 49, 77, 0.05);
      text-align: center;
      transition:
        transform 0.16s ease,
        border-color 0.16s ease,
        background 0.16s ease,
        box-shadow 0.16s ease;
    }

    .navbar__menu .nav-language__option > span:not(.nav-language__check) {
      width: 100%;
      overflow: hidden;
      font-size: 0.76rem;
      font-weight: 750;
      line-height: 1.15;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .navbar__menu .nav-language__option > small {
      order: -1;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 34px;
      height: 26px;
      padding-inline: 8px;
      border-radius: 999px;
      background: var(--color-cream-dark);
      color: #7c3c16;
      font-size: 0.68rem;
      font-weight: 850;
      letter-spacing: 0.06em;
    }

    .navbar__menu .nav-language__option.is-active {
      border-color: rgba(184, 79, 18, 0.38);
      background: linear-gradient(145deg, #fff8ec 0%, #ffe5d2 100%);
      box-shadow: 0 9px 20px rgba(184, 79, 18, 0.12);
    }

    .navbar__menu .nav-language__option.is-active > small {
      background: #b84f12;
      color: #fff;
    }

    .navbar__menu .nav-language__option.is-active .nav-language__check {
      position: absolute;
      top: 7px;
      right: 7px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      background: #b84f12;
      color: #fff;
      font-size: 0.66rem;
      font-weight: 900;
      line-height: 1;
    }

    html[lang="ar"] .navbar__menu .nav-language__option.is-active .nav-language__check {
      right: auto;
      left: 7px;
    }

    .navbar__menu .nav-language__option:active {
      transform: scale(0.97);
    }
  }
</style>

<header class="navbar {{ $isHomeNav ? 'navbar--hero' : 'navbar--public' }}" id="navbar">
  <div class="navbar__inner container">
    <a href="{{ $logoHref }}" class="navbar__logo" aria-label="{{ $logoLabel }}">
      <span class="navbar__logo-icon">
        @if (! empty($logoImageUrl))
          <img
            src="{{ $logoImageUrl }}"
            alt="{{ $logo['image_alt'] ?? $logoLabel }}"
            width="64"
            height="64"
            class="navbar__logo-image"
          />
        @else
          {{ $logo['icon'] ?? '🌙' }}
        @endif
      </span>

      <span class="navbar__logo-text">
        {{ $logo['line_1'] ?? __('pages.common.school_name') }}<br />
        <small>{{ $logo['line_2'] ?? __('pages.common.school_tagline') }}</small>
      </span>
    </a>

    <nav class="navbar__menu" id="navMenu" aria-label="{{ $siteNavbar['aria_label'] ?? __('pages.common.main_nav') }}">
      <ul>
        @foreach ($menuItems as $item)
          @php
            $isLanguageItem = ($item['type'] ?? null) === 'language';
            $hasMegaMenu = $isHomeNav
              && ! $isLanguageItem
              && ! empty($item['mega']['links'])
              && is_array($item['mega']['links']);
            $routePatterns = $item['route_patterns'] ?? [];
            $isActiveRoute = ! empty($routePatterns) && request()->routeIs(...$routePatterns);
            $isActiveHomeAnchor = $isHomeNav && $loop->first && ! $isLanguageItem;
            $isActive = $isActiveRoute || $isActiveHomeAnchor;
            $megaPanelId = 'navMegaPanel-' . $loop->index;
          @endphp

          <li
            class="nav-item{{ $isLanguageItem ? ' nav-language' : '' }}{{ $hasMegaMenu ? ' nav-mega' : '' }}"
            @if ($hasMegaMenu) data-nav-mega @endif
          >
            @if ($isLanguageItem)
              @php
                $currentOption = collect($item['options'] ?? [])->firstWhere('locale', $currentLocale);
              @endphp

              <button
                type="button"
                class="nav-link nav-language__button"
                aria-haspopup="true"
                aria-expanded="false"
              >
                {{ $item['label'] }}
                <small class="nav-link__badge">{{ $currentOption['short'] ?? strtoupper($currentLocale) }}</small>
              </button>

              <div class="nav-language__mobile-summary" aria-hidden="true">
                <div>
                  <strong class="nav-language__mobile-title">{{ $languageMobileTitle }}</strong>
                  <small class="nav-language__mobile-current">
                    {{ $languageMobileActive }} · {{ $currentOption['label'] ?? strtoupper($currentLocale) }}
                  </small>
                </div>
                <span class="nav-language__mobile-badge">{{ $currentOption['short'] ?? strtoupper($currentLocale) }}</span>
              </div>

              <div class="nav-language__panel" role="menu" aria-label="{{ $item['label'] }}">
                @foreach ($item['options'] ?? [] as $option)
                  <form method="POST" action="{{ route('language.switch', $option['locale']) }}" class="nav-language__form">
                    @csrf
                    <button
                      type="submit"
                      class="nav-language__option {{ $currentLocale === $option['locale'] ? 'is-active' : '' }}"
                      role="menuitem"
                      lang="{{ $option['locale'] }}"
                      @if ($currentLocale === $option['locale'])
                        aria-current="true"
                      @endif
                    >
                      <span>{{ $option['label'] }}</span>
                      <small>{{ $option['short'] }}</small>
                      <span class="nav-language__check" aria-hidden="true">✓</span>
                    </button>
                  </form>
                @endforeach
              </div>
            @elseif ($hasMegaMenu)
              <div class="nav-mega__trigger">
                <a
                  href="{{ $item['href'] }}"
                  class="nav-link {{ $isActive ? 'active' : '' }}"
                >
                  {{ $item['label'] }}
                </a>

                <button
                  type="button"
                  class="nav-mega__toggle"
                  data-nav-mega-toggle
                  aria-label="{{ $item['mega']['toggle_label'] ?? $item['label'] }}"
                  aria-haspopup="true"
                  aria-expanded="false"
                  aria-controls="{{ $megaPanelId }}"
                >
                  <svg viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </button>
              </div>

              <div
                class="nav-mega__panel"
                id="{{ $megaPanelId }}"
                data-nav-mega-panel
                aria-hidden="true"
                inert
              >
                <div class="nav-mega__intro">
                  <p class="nav-mega__eyebrow">{{ $item['mega']['eyebrow'] ?? $item['label'] }}</p>
                  <strong class="nav-mega__title">{{ $item['mega']['title'] ?? $item['label'] }}</strong>
                  @if (! empty($item['mega']['description']))
                    <p class="nav-mega__description">{{ $item['mega']['description'] }}</p>
                  @endif
                </div>

                <div class="nav-mega__links">
                  @foreach ($item['mega']['links'] as $megaLink)
                    <a href="{{ $megaLink['href'] }}" class="nav-mega__link">
                      <strong>{{ $megaLink['label'] }}</strong>
                      @if (! empty($megaLink['description']))
                        <small>{{ $megaLink['description'] }}</small>
                      @endif
                    </a>
                  @endforeach
                </div>
              </div>
            @elseif (! empty($item['disabled']))
              <span class="nav-link nav-link--dummy" aria-disabled="true">
                {{ $item['label'] }}
                @if (! empty($item['badge']))
                  <small class="nav-link__badge">{{ $item['badge'] }}</small>
                @endif
              </span>
            @else
              <a
                href="{{ $item['href'] }}"
                class="nav-link {{ $isActive ? 'active' : '' }}"
                @if ($isActiveRoute)
                  aria-current="page"
                @endif
              >
                {{ $item['label'] }}
                @if (! empty($item['badge']))
                  <small class="nav-link__badge">{{ $item['badge'] }}</small>
                @endif
              </a>
            @endif
          </li>
        @endforeach
      </ul>

      @if ($showCta)
        <a href="{{ $siteNavbar['cta']['href'] }}" class="btn btn--primary navbar__cta">
          {{ $siteNavbar['cta']['label'] }}
        </a>
      @endif
    </nav>

    <button
      class="hamburger"
      id="hamburgerBtn"
      aria-label="{{ $siteNavbar['mobile_open_label'] ?? __('pages.common.mobile_menu_open') }}"
      data-mobile-open-label="{{ $siteNavbar['mobile_open_label'] ?? __('pages.common.mobile_menu_open') }}"
      data-mobile-close-label="{{ $siteNavbar['mobile_close_label'] ?? __('pages.common.mobile_menu_close') }}"
      aria-expanded="false"
      aria-controls="navMenu"
    >
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>
