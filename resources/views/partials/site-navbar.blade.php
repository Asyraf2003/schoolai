<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">

  @include('partials.site-navbar.styles.language-modal')
  @include('partials.site-navbar.styles.responsive')
  @include('partials.site-navbar.styles.mega-roll')
  @include('partials.site-navbar.styles.desktop-mega-layout')

</style>

@if (app()->environment('production'))
  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-site-navbar-style="mega">{!! \Illuminate\Support\Facades\Vite::content('resources/css/pages/welcome-mega-menu.css') !!}</style>
@else
  @vite('resources/css/pages/welcome-mega-menu.css')
@endif

@include('partials.site-navbar.header')

@include('partials.site-navbar.mobile-navigation')

@include('partials.site-navbar.language-modal')

@include('partials.site-navbar.behavior')

@include('partials.site-navbar.mega-roll-script')
