{{-- PUBLIC_NAVBAR_DUMMY_FINAL --}}
<header class="public-topbar" aria-label="{{ __('pages.common.main_nav') }}">
  <div class="public-topbar__inner">
    <a href="{{ route('home') }}" class="public-brand" aria-label="{{ __('pages.common.nav.home') }}">
      <span class="public-brand__mark" aria-hidden="true">🌙</span>
      <span>
        <strong>{{ __('pages.common.school_name') }}</strong>
        <small>{{ __('pages.common.school_tagline') }}</small>
      </span>
    </a>

    <nav class="public-nav" aria-label="{{ __('pages.common.main_nav') }}">
      <a href="{{ route('home') }}" class="public-nav__link">{{ __('pages.common.nav.home') }}</a>
      <a href="{{ route('ppdb') }}" class="public-nav__link {{ request()->routeIs('ppdb') ? 'is-active' : '' }}" @if(request()->routeIs('ppdb')) aria-current="page" @endif>{{ __('pages.common.nav.ppdb') }}</a>
      <a href="{{ route('artikel') }}" class="public-nav__link {{ request()->routeIs('artikel') ? 'is-active' : '' }}" @if(request()->routeIs('artikel')) aria-current="page" @endif>{{ __('pages.common.nav.artikel') }}</a>
      <a href="{{ route('galeri') }}" class="public-nav__link {{ request()->routeIs('galeri') ? 'is-active' : '' }}" @if(request()->routeIs('galeri')) aria-current="page" @endif>{{ __('pages.common.nav.galeri') }}</a>
      <a href="{{ route('home') }}#kontak" class="public-nav__link">{{ __('pages.common.nav.contact') }}</a>
    </nav>

    <div class="public-language" aria-label="{{ __('pages.common.language_label') }}">
      <a href="{{ route('language.switch', 'id') }}" class="{{ app()->getLocale() === 'id' ? 'is-active' : '' }}">ID</a>
      <a href="{{ route('language.switch', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
    </div>
  </div>
</header>
