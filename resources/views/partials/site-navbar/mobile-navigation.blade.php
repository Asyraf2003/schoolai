<div
  class="mobile-navigation-layer"
  id="navMenu"
  data-mobile-navigation-layer
  role="dialog"
  aria-modal="true"
  aria-label="{{ $siteNavbar['aria_label'] ?? __('pages.common.main_nav') }}"
  aria-hidden="true"
  inert
  hidden
>
  <button
    type="button"
    class="mobile-navigation-layer__backdrop"
    data-mobile-navigation-close
    aria-label="{{ $siteNavbar['mobile_close_label'] ?? __('pages.common.mobile_menu_close') }}"
  ></button>

  <nav class="mobile-navigation-layer__menu" aria-label="{{ $siteNavbar['aria_label'] ?? __('pages.common.main_nav') }}">
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
          $megaPanelId = 'mobileNavMegaPanel-' . $loop->index;
        @endphp

        <li
          class="nav-item{{ $isLanguageItem ? ' nav-language' : '' }}{{ $hasMegaMenu ? ' nav-mega' : '' }}"
          data-mobile-navigation-item
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
              @if ($isActiveRoute) aria-current="page" @endif
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
      <a
        href="{{ $siteNavbar['cta']['href'] }}"
        class="btn btn--primary navbar__cta"
        data-mobile-navigation-item
        data-text-role="action"
      >
        {{ $siteNavbar['cta']['label'] }}
      </a>
    @endif
  </nav>
</div>
