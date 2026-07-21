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
  $languageModalTitle = match ($currentLocale) {
      'ar' => 'اختر اللغة',
      'en' => 'Choose language',
      default => 'Pilih bahasa',
  };
  $languageModalClose = match ($currentLocale) {
      'ar' => 'إغلاق اختيار اللغة',
      'en' => 'Close language chooser',
      default => 'Tutup pilihan bahasa',
  };
@endphp

<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  .nav-language__flag {
    width: 100%;
    height: 100%;
    display: block;
    overflow: hidden;
    border-radius: inherit;
  }

  .nav-language__flag svg {
    width: 100%;
    height: 100%;
    display: block;
  }

  .navbar--public .nav-language__current-flag {
    border-color: rgba(31, 46, 43, 0.12);
  }

  .language-modal {
    position: fixed;
    inset: 0;
    z-index: 120;
    display: grid;
    place-items: center;
    padding: clamp(20px, 4vw, 48px);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition:
      opacity 220ms ease,
      visibility 0s linear 260ms;
  }

  .language-modal.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transition-delay: 0s;
  }

  .language-modal__backdrop {
    position: absolute;
    inset: 0;
    border: 0;
    background: rgba(2, 16, 14, 0.76);
    -webkit-backdrop-filter: blur(14px) saturate(115%);
    backdrop-filter: blur(14px) saturate(115%);
  }

  .language-modal__dialog {
    position: relative;
    z-index: 1;
    width: min(760px, calc(100vw - 40px));
    min-height: 340px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: clamp(38px, 6vw, 72px) clamp(24px, 6vw, 64px);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.72);
    border-radius: 32px;
    color: #17362f;
    background:
      radial-gradient(circle at 50% 0%, rgba(247, 178, 75, 0.18), transparent 42%),
      rgba(255, 250, 241, 0.98);
    box-shadow: 0 36px 110px rgba(0, 0, 0, 0.38);
    opacity: 0;
    transform: translateY(18px) scale(0.94);
    transition:
      opacity 220ms ease,
      transform 360ms cubic-bezier(0.22, 1, 0.36, 1);
  }

  .language-modal.is-open .language-modal__dialog {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  .language-modal__close {
    position: absolute;
    inset-block-start: 18px;
    inset-inline-end: 18px;
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(23, 54, 47, 0.14);
    border-radius: 50%;
    color: #17362f;
    background: rgba(255, 255, 255, 0.72);
    font-size: 1.65rem;
    line-height: 1;
    cursor: pointer;
  }

  .language-modal__title {
    margin: 0 0 clamp(28px, 5vw, 48px);
    color: #17362f;
    font-family: var(--font-display);
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    font-weight: 820;
    text-align: center;
  }

  .language-modal__options {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    gap: clamp(22px, 5vw, 54px);
  }

  .language-modal__form {
    margin: 0;
  }

  .language-modal__option {
    display: grid;
    justify-items: center;
    gap: 14px;
    border: 0;
    color: #294c44;
    background: transparent;
    font: inherit;
    cursor: pointer;
  }

  .language-modal__flag {
    width: clamp(96px, 10vw, 142px);
    height: clamp(96px, 10vw, 142px);
    display: block;
    overflow: hidden;
    border: 5px solid rgba(255, 255, 255, 0.96);
    border-radius: 50%;
    background: #fff;
    box-shadow:
      0 16px 38px rgba(20, 45, 39, 0.2),
      0 0 0 1px rgba(23, 54, 47, 0.08);
    transition:
      transform 180ms ease,
      border-color 180ms ease,
      box-shadow 180ms ease;
  }

  .language-modal__option:hover .language-modal__flag,
  .language-modal__option:focus-visible .language-modal__flag {
    border-color: #f7c66f;
    box-shadow:
      0 0 0 6px rgba(247, 198, 111, 0.18),
      0 22px 46px rgba(20, 45, 39, 0.24);
    transform: translateY(-5px) scale(1.03);
  }

  .language-modal__option.is-active .language-modal__flag {
    border-color: #f7b24b;
    box-shadow:
      0 0 0 7px rgba(247, 178, 75, 0.2),
      0 20px 42px rgba(20, 45, 39, 0.22);
  }

  .language-modal__label {
    font-size: 0.82rem;
    font-weight: 800;
  }

  @media (max-width: 1180px) {
    .navbar__menu .nav-language {
      width: 100%;
      margin-block-start: 10px;
      padding-block-start: 16px;
      border-block-start: 1px solid rgba(51, 49, 77, 0.1);
    }

    .navbar__menu .nav-language__button {
      display: flex !important;
      width: 100%;
      justify-content: flex-start !important;
      color: #17362f;
    }

    .navbar__menu .nav-language__current-flag {
      display: none;
    }
  }

  @media (max-width: 640px) {
    .language-modal {
      padding: 16px;
    }

    .language-modal__dialog {
      width: min(100%, 430px);
      min-height: 300px;
      padding: 58px 18px 38px;
      border-radius: 26px;
    }

    .language-modal__title {
      margin-bottom: 30px;
      font-size: 1.45rem;
    }

    .language-modal__options {
      gap: clamp(12px, 4vw, 22px);
    }

    .language-modal__flag {
      width: clamp(76px, 23vw, 104px);
      height: clamp(76px, 23vw, 104px);
      border-width: 4px;
    }

    .language-modal__label {
      font-size: 0.72rem;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .language-modal,
    .language-modal__dialog,
    .language-modal__flag {
      transition-duration: 0.01ms !important;
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
                data-language-modal-open
                aria-haspopup="dialog"
                aria-controls="languageModal"
              >
                <span>{{ $item['label'] }}</span>
                <span class="nav-language__current-flag" aria-hidden="true">
                  @include('partials.language-flag', ['locale' => $currentOption['locale'] ?? $currentLocale])
                </span>
              </button>
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

<div
  class="language-modal"
  id="languageModal"
  data-language-modal
  aria-hidden="true"
>
  <button
    type="button"
    class="language-modal__backdrop"
    data-language-modal-close
    aria-label="{{ $languageModalClose }}"
  ></button>

  <div
    class="language-modal__dialog"
    role="dialog"
    aria-modal="true"
    aria-labelledby="languageModalTitle"
    tabindex="-1"
  >
    <button
      type="button"
      class="language-modal__close"
      data-language-modal-close
      aria-label="{{ $languageModalClose }}"
    >
      ×
    </button>

    <h2 class="language-modal__title" id="languageModalTitle">{{ $languageModalTitle }}</h2>

    <div class="language-modal__options">
      @foreach ($languageItem['options'] ?? [] as $option)
        <form method="POST" action="{{ route('language.switch', $option['locale']) }}" class="language-modal__form">
          @csrf
          <button
            type="submit"
            class="language-modal__option {{ $currentLocale === $option['locale'] ? 'is-active' : '' }}"
            lang="{{ $option['locale'] }}"
            aria-label="{{ $option['label'] }}"
            @if ($currentLocale === $option['locale']) aria-current="true" @endif
          >
            <span class="language-modal__flag">
              @include('partials.language-flag', ['locale' => $option['locale']])
            </span>
            <span class="language-modal__label">{{ $option['label'] }}</span>
          </button>
        </form>
      @endforeach
    </div>
  </div>
</div>

<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  document.addEventListener('DOMContentLoaded', function () {
    var modal = document.querySelector('[data-language-modal]');
    var triggers = Array.prototype.slice.call(document.querySelectorAll('[data-language-modal-open]'));

    if (!modal || !triggers.length) return;

    var dialog = modal.querySelector('.language-modal__dialog');
    var closeControls = Array.prototype.slice.call(modal.querySelectorAll('[data-language-modal-close]'));
    var hamburger = document.getElementById('hamburgerBtn');
    var navMenu = document.getElementById('navMenu');
    var navOverlay = document.getElementById('navOverlay');
    var lastFocused = null;
    var previousOverflow = '';

    function closeMobileMenu() {
      if (!navMenu || !navMenu.classList.contains('active')) return;

      navMenu.classList.remove('active');
      if (navOverlay) navOverlay.classList.remove('active');
      document.body.style.overflow = '';

      if (hamburger) {
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.setAttribute(
          'aria-label',
          hamburger.getAttribute('data-mobile-open-label') || 'Open menu'
        );
      }
    }

    function openLanguageModal(trigger) {
      var openedFromMobileMenu = navMenu && navMenu.classList.contains('active');
      lastFocused = openedFromMobileMenu && hamburger
        ? hamburger
        : (trigger || document.activeElement);

      closeMobileMenu();
      previousOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');

      window.requestAnimationFrame(function () {
        if (dialog) dialog.focus({ preventScroll: true });
      });
    }

    function closeLanguageModal() {
      if (!modal.classList.contains('is-open')) return;

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = previousOverflow;

      if (lastFocused && document.documentElement.contains(lastFocused)) {
        lastFocused.focus({ preventScroll: true });
      }
    }

    triggers.forEach(function (trigger) {
      trigger.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        openLanguageModal(trigger);
      });
    });

    closeControls.forEach(function (control) {
      control.addEventListener('click', closeLanguageModal);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('is-open')) {
        event.preventDefault();
        closeLanguageModal();
      }
    });
  });
</script>
