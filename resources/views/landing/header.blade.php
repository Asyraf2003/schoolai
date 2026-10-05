<header class="site-header" data-header>
    <div class="site-header__bar">
        <a class="site-header__brand" href="{{ $logoHref }}" aria-label="{{ $logoLabel }}">
            <img src="{{ $logoImageUrl }}" alt="{{ $logo['image_alt'] ?? $logoLabel }}" width="64" height="64">
        </a>
        <button class="site-header__audio" type="button" data-audio hidden aria-pressed="false"
            aria-label="{{ __('shared.navbar.audio.enable_label') }}"
            data-label-on="{{ __('shared.navbar.audio.on') }}" data-label-off="{{ __('shared.navbar.audio.off') }}"
            data-action-on="{{ __('shared.navbar.audio.disable_label') }}"
            data-action-off="{{ __('shared.navbar.audio.enable_label') }}">
            <span data-audio-label>{{ __('shared.navbar.audio.off') }}</span>
            <canvas class="site-header__audio-wave" data-audio-wave aria-hidden="true"></canvas>
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
                            <details class="site-header__language" data-language>
                                <summary aria-controls="header-language-dialog" aria-expanded="false"><span data-menu-label>{{ $item['label'] }}</span></summary>
                                <dialog id="header-language-dialog" class="site-header__languages" open aria-label="{{ __('shared.navbar.language_modal.title') }}">
                                    <div class="site-header__language-options">
                                    @foreach ($item['options'] as $option)
                                        <form method="POST" action="{{ route('language.switch', $option['locale']) }}">
                                            @csrf
                                            <button type="submit" data-locale="{{ $option['locale'] }}" @if (app()->getLocale() === $option['locale']) aria-current="true" @endif>
                                                <img class="site-header__flag" src="{{ config('media.static.language_flags.'.$option['locale']) }}" width="28" height="28" alt="">
                                                <span class="sr-only">{{ $option['label'] }}</span>
                                            </button>
                                        </form>
                                    @endforeach
                                    </div>
                                </dialog>
                            </details>
                        @elseif ($item['has_mega_menu'])
                            <details class="site-header__group" data-panel="{{ $loop->index }}">
                                <summary><span data-menu-label>{{ $item['label'] }}</span><svg class="site-header__chevron" viewBox="0 0 16 16" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg></summary>
                                <div class="site-header__panel">
                                    <figure class="site-header__media" data-media-fallback>
                                        <figcaption>{{ $item['mega']['title'] ?? $item['mega_media_alt'] }}</figcaption>
                                        <img src="{{ $item['mega_media_url'] }}" alt="{{ $item['mega_media_alt'] }}"
                                            width="1080" height="720" loading="lazy" decoding="async">
                                    </figure>
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
