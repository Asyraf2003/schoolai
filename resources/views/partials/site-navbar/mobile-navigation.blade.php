<div
  class="mobile-navigation-layer"
  id="navMenu"
  data-mobile-navigation-layer
  aria-hidden="true"
  inert
  hidden
>
  <button
    type="button"
    class="mobile-navigation-layer__backdrop"
    data-mobile-navigation-close
    tabindex="-1"
    aria-label="{{ $siteNavbar['mobile_close_label'] ?? __('pages.common.mobile_menu_close') }}"
  ></button>

  <nav class="mobile-navigation-layer__menu" aria-label="{{ $siteNavbar['aria_label'] ?? __('pages.common.main_nav') }}">
    <ul>
      @foreach ($menuItems as $item)
        <li
          class="nav-item{{ $item['is_language'] ? ' nav-language' : '' }}{{ $item['is_login'] ? ' nav-login' : '' }}{{ $item['has_mega_menu'] ? ' nav-mega' : '' }}"
          data-mobile-navigation-item
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
              aria-controls="{{ $item['mobile_panel_id'] }}"
            >
              <span data-nav-roll="main">{{ $item['label'] }}</span>
              <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </button>

            <div
              class="nav-mega__panel"
              id="{{ $item['mobile_panel_id'] }}"
              data-nav-mega-panel
              aria-hidden="true"
              inert
            >
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
              @if ($item['is_active_route']) aria-current="page" @endif
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
      <a
        href="{{ $siteNavbar['cta']['href'] }}"
        class="btn btn--primary navbar__cta"
        data-mobile-navigation-item
        data-text-role="action"
      >
        <span data-nav-roll="main">{{ $siteNavbar['cta']['label'] }}</span>
      </a>
    @endif
  </nav>
</div>
