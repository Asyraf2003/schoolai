<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">

  @include('partials.site-navbar.styles.language-modal')
  @include('partials.site-navbar.styles.responsive')
  @include('partials.site-navbar.styles.mega-roll')
  @include('partials.site-navbar.styles.desktop-mega-layout')

</style>

@include('partials.site-navbar.header')

@include('partials.site-navbar.mobile-navigation')

@include('partials.site-navbar.language-modal')

@include('partials.site-navbar.behavior')

@include('partials.site-navbar.mega-roll-script')
