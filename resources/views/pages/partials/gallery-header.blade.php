<header class="gallery-header gallery-wrap">
    <a class="gallery-back" href="{{ route('home') }}" aria-label="{{ __('gallery_v3.back') }}">
        <span class="gallery-back__arrow" aria-hidden="true">⟵</span><span>{{ __('gallery_v3.back') }}</span>
    </a>
    <img class="gallery-header__logo" src="{{ $navigation['logoImageUrl'] }}" alt="{{ $navigation['logoLabel'] }}" width="64" height="64">
    <div class="gallery-header__controls">
        <button class="gallery-sound" type="button" data-gallery-sound disabled aria-pressed="false"
            aria-label="{{ __('shared.navbar.audio.enable_label') }}" data-text-on="{{ __('shared.navbar.audio.on') }}" data-text-off="{{ __('shared.navbar.audio.off') }}" data-label-on="{{ __('shared.navbar.audio.disable_label') }}" data-label-off="{{ __('shared.navbar.audio.enable_label') }}">
            <span data-audio-label>{{ __('shared.navbar.audio.off') }}</span>
            <canvas class="site-header__audio-wave" data-audio-wave aria-hidden="true"></canvas>
        </button>
        <a class="gallery-article" href="{{ route('artikel') }}" aria-label="{{ __('pages.common.nav.artikel') }}"><span aria-hidden="true">Aa</span><span>{{ __('pages.common.nav.artikel') }}</span></a>
    </div>
</header>
