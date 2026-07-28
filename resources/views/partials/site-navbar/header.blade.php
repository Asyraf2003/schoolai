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
            $hasMegaMenu = ! $isLanguageItem
              && ! empty($item['mega']['links'])
              && is_array($item['mega']['links']);
            $routePatterns = $item['route_patterns'] ?? [];
            $isActiveRoute = ! empty($routePatterns) && request()->routeIs(...$routePatterns);
            $isActiveHomeAnchor = $isHomeNav && $loop->first && ! $isLanguageItem;
            $isActive = $isActiveRoute || $isActiveHomeAnchor;
            $megaPanelId = 'navMegaPanel-' . $loop->index;
            $itemMegaMediaUrl = $item['mega']['media_url'] ?? $megaMediaUrl;
            $itemMegaMediaAlt = $item['mega']['media_alt'] ?? $megaMediaAlt;
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
                data-text-role="action"
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
                data-text-role="action"
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
                    src="{{ $itemMegaMediaUrl }}"
                    alt="{{ $itemMegaMediaAlt }}"
                    width="720"
                    height="540"
                    loading="lazy"
                    decoding="async"
                  />
                  <span class="nav-mega__media-shade" aria-hidden="true"></span>
                </div>

                <div class="nav-mega__intro">
                  <p class="nav-mega__eyebrow" data-text-role="label">{{ $item['mega']['eyebrow'] ?? $item['label'] }}</p>
                  <strong class="nav-mega__title" data-text-role="component-title">{{ $item['mega']['title'] ?? $item['label'] }}</strong>
                  @if (! empty($item['mega']['description']))
                    <p class="nav-mega__description" data-text-role="description">{{ $item['mega']['description'] }}</p>
                  @endif
                </div>

                <div class="nav-mega__links">
                  @foreach ($item['mega']['links'] as $megaLink)
                    <a href="{{ $megaLink['href'] }}" class="nav-mega__link">
                      <strong data-text-role="action">{{ $megaLink['label'] }}</strong>
                      @if (! empty($megaLink['description']))
                        <small data-text-role="description">{{ $megaLink['description'] }}</small>
                      @endif
                    </a>
                  @endforeach
                </div>
              </div>
            @elseif (! empty($item['disabled']))
              <span class="nav-link nav-link--dummy" data-text-role="action" aria-disabled="true">
                {{ $item['label'] }}
                @if (! empty($item['badge']))
                  <small class="nav-link__badge">{{ $item['badge'] }}</small>
                @endif
              </span>
            @else
              <a
                href="{{ $item['href'] }}"
                class="nav-link {{ $isActive ? 'active' : '' }}"
                data-text-role="action"
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
        <a href="{{ $siteNavbar['cta']['href'] }}" class="btn btn--primary navbar__cta" data-text-role="action">
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
