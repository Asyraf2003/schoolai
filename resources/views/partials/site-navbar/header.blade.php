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

    <nav class="navbar__menu" id="desktopNavMenu" aria-label="{{ $siteNavbar['aria_label'] ?? __('pages.common.main_nav') }}">
      <ul>
        @if ($isHomeNav)
          <li class="nav-item nav-hero-audio nav-hero-audio--desktop">
            <button
              type="button"
              class="nav-link nav-hero-audio__text"
              data-hero-audio
              data-hero-audio-label-off="{{ __('shared.navbar.audio.enable_label') }}"
              data-hero-audio-label-on="{{ __('shared.navbar.audio.disable_label') }}"
              aria-pressed="false"
              aria-label="{{ __('shared.navbar.audio.enable_label') }}"
            >
              <span class="nav-hero-audio__label nav-hero-audio__label--off">{{ __('shared.navbar.audio.off') }}</span>
              <span class="nav-hero-audio__label nav-hero-audio__label--on">{{ __('shared.navbar.audio.on') }}</span>
            </button>
          </li>
        @endif

        @foreach ($menuItems as $item)
          <li
            class="nav-item{{ $item['is_language'] ? ' nav-language' : '' }}{{ $item['is_login'] ? ' nav-login' : '' }}{{ $item['has_mega_menu'] ? ' nav-mega' : '' }}"
            @if ($item['has_mega_menu']) data-nav-mega @endif
          >
            @if ($item['is_language'])
              <button
                type="button"
                class="nav-link nav-language__button"
                data-language-modal-open
                data-text-role="action"
                aria-haspopup="dialog"
                aria-controls="languageModal"
              >
                <span data-nav-roll="main">{{ $item['label'] }}</span>
                <span class="nav-language__current-flag" aria-hidden="true">
                  @include('partials.language-flag', ['locale' => $item['current_option']['locale'] ?? $currentLocale])
                </span>
              </button>
            @elseif ($item['has_mega_menu'])
              <button
                type="button"
                class="nav-link nav-mega__trigger {{ $item['is_active'] ? 'active' : '' }}"
                data-nav-mega-toggle
                data-text-role="action"
                aria-label="{{ $item['mega']['toggle_label'] ?? $item['label'] }}"
                aria-haspopup="true"
                aria-expanded="false"
                aria-controls="{{ $item['desktop_panel_id'] }}"
              >
                <span data-nav-roll="main">{{ $item['label'] }}</span>
                <svg viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
              </button>

              <div
                class="nav-mega__panel"
                id="{{ $item['desktop_panel_id'] }}"
                data-nav-mega-panel
                aria-hidden="true"
                inert
              >
                <div class="nav-mega__media">
                  <img
                    src="{{ $item['mega_media_url'] }}"
                    alt="{{ $item['mega_media_alt'] }}"
                    width="720"
                    height="540"
                    loading="lazy"
                    decoding="async"
                  />
                  <span class="nav-mega__media-shade" aria-hidden="true"></span>
                </div>

                <div class="nav-mega__links">
                  @foreach ($item['mega']['links'] as $megaLink)
                    <a href="{{ $megaLink['href'] }}" class="nav-mega__link">
                      <strong data-nav-roll="sub" data-text-role="action">{{ $megaLink['label'] }}</strong>
                      @if (! empty($megaLink['description']))
                        <small data-text-role="description">{{ $megaLink['description'] }}</small>
                      @endif
                    </a>
                  @endforeach
                </div>
              </div>
            @elseif (! empty($item['disabled']))
              <span class="nav-link nav-link--dummy" data-text-role="action" aria-disabled="true">
                <span data-nav-roll="main">{{ $item['label'] }}</span>
                @if (! empty($item['badge']))
                  <small class="nav-link__badge">{{ $item['badge'] }}</small>
                @endif
              </span>
            @else
              <a
                href="{{ $item['href'] }}"
                class="nav-link {{ $item['is_active'] ? 'active' : '' }}"
                data-text-role="action"
                @if ($item['is_active_route'])
                  aria-current="page"
                @endif
              >
                <span data-nav-roll="main">{{ $item['label'] }}</span>
                @if (! empty($item['badge']))
                  <small class="nav-link__badge">{{ $item['badge'] }}</small>
                @endif
              </a>
            @endif
          </li>
        @endforeach
      </ul>

      @if ($showCta)
        <a href="{{ $siteNavbar['cta']['href'] }}" class="btn btn--primary navbar__cta" data-text-role="action">
          <span data-nav-roll="main">{{ $siteNavbar['cta']['label'] }}</span>
        </a>
      @endif
    </nav>

    @if ($isHomeNav)
      <div class="navbar__mobile-actions">
        <button
          type="button"
          class="navbar__hero-audio-icon"
          data-hero-audio
          data-hero-audio-label-off="{{ __('shared.navbar.audio.enable_label') }}"
          data-hero-audio-label-on="{{ __('shared.navbar.audio.disable_label') }}"
          aria-pressed="false"
          aria-label="{{ __('shared.navbar.audio.enable_label') }}"
        >
          <svg class="navbar__hero-audio-glyph" viewBox="0 0 44 20" aria-hidden="true">
            <path class="navbar__hero-audio-line" d="M8 10H36" />
            <path
              class="navbar__hero-audio-snake"
              d="M6 10C10 3 18 3 22 10C26 17 34 17 38 10"
            >
              <animate
                attributeName="d"
                dur="820ms"
                repeatCount="indefinite"
                values="M6 10C10 3 18 3 22 10C26 17 34 17 38 10;M6 10C10 17 18 17 22 10C26 3 34 3 38 10;M6 10C10 3 18 3 22 10C26 17 34 17 38 10"
              />
            </path>
          </svg>
        </button>

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
    @else
      <button
        class="hamburger"
        id="hamburgerBtn"
        aria-label="{{ $siteNavbar['mobile_open_label'] ?? __('pages.common.mobile_menu_open') }}"
        data-mobile-open-label="{{ $siteNavbar['mobile_open_label'] ?? __('pages.common.mobile_menu_close') }}"
        data-mobile-close-label="{{ $siteNavbar['mobile_close_label'] ?? __('pages.common.mobile_menu_close') }}"
        aria-expanded="false"
        aria-controls="navMenu"
      >
        <span></span><span></span><span></span>
      </button>
    @endif
  </div>
</header>
