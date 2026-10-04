<header class="site-header" data-header>
    <div class="site-header__bar">
        <a class="site-header__brand" href="{{ $logoHref }}" aria-label="{{ $logoLabel }}">
            <img src="{{ $logoImageUrl }}" alt="{{ $logo['image_alt'] ?? $logoLabel }}" width="64" height="64">
            <span>{{ $logo['line_1'] }}<small>{{ $logo['line_2'] }}</small></span>
        </a>
        <button class="site-header__audio" type="button" data-audio hidden aria-pressed="false"
            aria-label="{{ __('shared.navbar.audio.enable_label') }}"
            data-label-on="{{ __('shared.navbar.audio.on') }}" data-label-off="{{ __('shared.navbar.audio.off') }}"
            data-action-on="{{ __('shared.navbar.audio.disable_label') }}"
            data-action-off="{{ __('shared.navbar.audio.enable_label') }}">
            <span data-audio-label>{{ __('shared.navbar.audio.off') }}</span>
            <svg class="site-header__audio-glyph" viewBox="0 0 44 20" aria-hidden="true">
                <path class="site-header__audio-line" d="M8 10H36"/>
                <path class="site-header__audio-wave" d="M6 10C10 3 18 3 22 10C26 17 34 17 38 10"/>
            </svg>
        </button>
        <button class="site-header__toggle" type="button" data-menu-toggle hidden
            aria-controls="landing-navigation" aria-expanded="false"
            aria-label="{{ __('pages.common.mobile_menu_open') }}"
            data-open-label="{{ __('pages.common.mobile_menu_open') }}"
            data-close-label="{{ __('pages.common.mobile_menu_close') }}">
            <span></span><span></span><span></span>
        </button>
        <nav class="site-header__navigation" id="landing-navigation" aria-label="{{ $siteNavbar['aria_label'] }}">
            <ul class="site-header__items">
                @foreach ($menuItems as $item)
                    <li>
                        @if ($item['is_language'])
                            <span class="site-header__language" lang="en">{{ $item['label'] }} <img class="site-header__flag" src="{{ config('media.static.language_flags.en') }}" width="28" height="28" alt="English"></span>
                        @elseif ($item['has_mega_menu'])
                            <details class="site-header__group" data-panel="{{ $loop->index }}">
                                <summary><span data-menu-label>{{ $item['label'] }}</span><span aria-hidden="true">⌄</span></summary>
                                <div class="site-header__panel">
                                    <img src="{{ $item['mega_media_url'] }}" alt="{{ $item['mega_media_alt'] }}"
                                        width="720" height="540" loading="lazy" decoding="async">
                                    <div class="site-header__links">
                                        @foreach ($item['mega']['links'] as $link)
                                            <a href="{{ $link['href'] }}">
                                                <strong data-menu-label>{{ $link['label'] }}</strong>
                                                @if (! empty($link['description']))<small>{{ $link['description'] }}</small>@endif
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </details>
                        @else
                            <a href="{{ $item['href'] }}" @if ($item['is_active']) aria-current="page" @endif>
                                <span data-menu-label>{{ $item['label'] }}</span>
                            </a>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</header>
