{{-- SHARED_NAVBAR_COMPONENT_FINAL --}}
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
          'label' => __('pages.common.nav.language') !== 'pages.common.nav.language' ? __('pages.common.nav.language') : 'Bahasa',
          'type' => 'language',
          'options' => [
              ['locale' => 'id', 'label' => 'Indonesia', 'short' => 'ID'],
              ['locale' => 'en', 'label' => 'English', 'short' => 'EN'],
          ],
      ];
  }

  $homeUrl = route('home');
  $contactUrl = $isHomeNav ? '#kontak' : $homeUrl . '#kontak';

  if ($isHomeNav) {
      $menuItems = $siteNavbar['items'] ?? [];
  } else {
      $menuItems = [
          [
              'label' => __('pages.common.nav.home'),
              'href' => $homeUrl,
              'route_patterns' => ['home'],
          ],
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
  $logoHref = $isHomeNav ? ($logo['href'] ?? '#beranda') : $homeUrl;
  $logoLabel = trim((string) (($logo['line_1'] ?? __('pages.common.school_name')) . ' ' . ($logo['line_2'] ?? '')));
  $showCta = $isHomeNav && ! empty($siteNavbar['cta']);
@endphp

<!-- SHARED_NAVBAR_COMPONENT_FINAL -->
<header class="navbar" id="navbar">
  <div class="navbar__inner container">
    <a href="{{ $logoHref }}" class="navbar__logo" aria-label="{{ $logoLabel }}">
      <span class="navbar__logo-icon">
        @if (! empty($logo['image_url']))
          <img
            src="{{ $logo['image_url'] }}"
            alt="{{ $logo['image_alt'] ?? $logoLabel }}"
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
            $routePatterns = $item['route_patterns'] ?? [];
            $isActiveRoute = ! empty($routePatterns) && request()->routeIs(...$routePatterns);
            $isActiveHomeAnchor = $isHomeNav && $loop->first && ! $isLanguageItem;
            $isActive = $isActiveRoute || $isActiveHomeAnchor;
          @endphp

          <li class="{{ $isLanguageItem ? 'nav-language' : '' }}">
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

              <div class="nav-language__panel" role="menu" aria-label="{{ $item['label'] }}">
                @foreach ($item['options'] ?? [] as $option)
                  <a
                    href="{{ route('language.switch', $option['locale']) }}"
                    class="nav-language__option {{ $currentLocale === $option['locale'] ? 'is-active' : '' }}"
                    role="menuitem"
                    @if ($currentLocale === $option['locale'])
                      aria-current="true"
                    @endif
                  >
                    <span>{{ $option['label'] }}</span>
                    <small>{{ $option['short'] }}</small>
                  </a>
                @endforeach
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
      aria-label="{{ $siteNavbar['mobile_open_label'] ?? 'Buka menu' }}"
      aria-expanded="false"
      aria-controls="navMenu"
    >
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<div class="nav-overlay" id="navOverlay"></div>
<!-- /SHARED_NAVBAR_COMPONENT_FINAL -->
