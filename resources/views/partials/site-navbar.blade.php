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
  $megaMediaUrl = $siteNavbar['mega_media_url'] ?? asset('media/home/hero-school.png');
  $megaMediaAlt = $siteNavbar['mega_media_alt'] ?? $logoLabel;
@endphp

<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  .nav-language__mobile-summary,
  .nav-language__check {
    display: none;
  }

  .nav-language__flag,
  .nav-language__option > .nav-language__flag {
    width: 100%;
    height: 100%;
    display: block;
    position: static;
    overflow: hidden;
    border-radius: inherit;
    clip-path: none;
    white-space: normal;
  }

  .nav-language__flag svg {
    width: 100%;
    height: 100%;
    display: block;
  }

  .navbar--public .nav-language__current-flag {
    width: 24px;
    height: 24px;
    flex: 0 0 24px;
    overflow: hidden;
    border: 2px solid rgba(31, 46, 43, 0.12);
    border-radius: 50%;
  }

  .navbar--public .nav-language__panel {
    min-width: max-content;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 2px;
    border: 0;
    background: transparent;
    box-shadow: none;
  }

  .navbar--public .nav-language__option {
    width: 48px;
    height: 48px;
    min-height: 48px;
    padding: 0;
    overflow: hidden;
    border: 3px solid #fff;
    border-radius: 50%;
    box-shadow: 0 8px 20px rgba(31, 46, 43, 0.14);
  }

  .navbar--public .nav-language__option.is-active {
    border-color: #e9a53b;
    box-shadow:
      0 0 0 3px rgba(233, 165, 59, 0.18),
      0 8px 20px rgba(31, 46, 43, 0.16);
  }

  @media (max-width: 767px) {
    .navbar__menu .nav-language {
      width: 100%;
      margin-top: 10px;
      padding-top: 16px;
      border-top: 1px solid rgba(51, 49, 77, 0.1);
    }

    .navbar__menu .nav-language__button,
    .navbar__menu .nav-language__mobile-summary {
      display: none;
    }

    .navbar__menu .nav-language__panel,
    .navbar__menu .nav-language.is-open .nav-language__panel {
      position: static !important;
      inset: auto !important;
      width: auto !important;
      min-width: 0 !important;
      display: flex !important;
      align-items: center;
      justify-content: flex-start;
      gap: 12px;
      margin: 0 !important;
      padding: 2px !important;
      border: 0 !important;
      background: transparent !important;
      box-shadow: none !important;
      opacity: 1 !important;
      visibility: visible !important;
      pointer-events: auto !important;
      transform: none !important;
    }

    .navbar__menu .nav-language__form {
      flex: 0 0 auto;
      margin: 0;
    }

    .navbar__menu .nav-language__option {
      width: 52px;
      height: 52px;
      min-height: 52px;
      display: grid;
      place-items: center;
      padding: 0;
      overflow: hidden;
      border: 3px solid rgba(255, 255, 255, 0.88);
      border-radius: 50%;
      background: transparent;
      box-shadow: 0 8px 20px rgba(20, 45, 39, 0.14);
      transition:
        transform 0.18s ease,
        box-shadow 0.18s ease,
        border-color 0.18s ease;
    }

    .navbar__menu .nav-language__option.is-active {
      border-color: #e9a53b;
      box-shadow:
        0 0 0 3px rgba(233, 165, 59, 0.2),
        0 9px 22px rgba(20, 45, 39, 0.18);
    }

    .navbar__menu .nav-language__option:active {
      transform: scale(0.94);
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
                <span>{{ $item['label'] }}</span>
                <span class="nav-language__current-flag">
                  @include('partials.language-flag', ['locale' => $currentOption['locale'] ?? $currentLocale])
                </span>
              </button>

              <div class="nav-language__panel" role="menu" aria-label="{{ $item['label'] }}">
                @foreach ($item['options'] ?? [] as $option)
                  <form method="POST" action="{{ route('language.switch', $option['locale']) }}" class="nav-language__form">
                    @csrf
                    <button
                      type="submit"
                      class="nav-language__option {{ $currentLocale === $option['locale'] ? 'is-active' : '' }}"
                      role="menuitem"
                      lang="{{ $option['locale'] }}"
                      aria-label="{{ $option['label'] }}"
                      title="{{ $option['label'] }}"
                      @if ($currentLocale === $option['locale'])
                        aria-current="true"
                      @endif
                    >
                      @include('partials.language-flag', ['locale' => $option['locale']])
                      <span class="sr-only">{{ $option['label'] }}</span>
                    </button>
                  </form>
                @endforeach
              </div>
            @elseif ($hasMegaMenu)
              <button
                type="button"
                class="nav-link nav-mega__trigger {{ $isActive ? 'active' : '' }}"
                data-nav-mega-toggle
                aria-label="{{ $item['mega']['toggle_label'] ?? $item['label'] }}"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="{{ $megaPanelId }}"
              >
                <span>{{ $item['label'] }}</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>

              <div
                class="nav-mega__panel"
                id="{{ $megaPanelId }}"
                data-nav-mega-panel
                aria-hidden="true"
                inert
              >
                <div class="nav-mega__media">
                  <img
                    src="{{ $megaMediaUrl }}"
                    alt="{{ $megaMediaAlt }}"
                    width="720"
                    height="540"
                    loading="lazy"
                    decoding="async"
                  />
                  <span class="nav-mega__media-shade" aria-hidden="true"></span>
                </div>

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
