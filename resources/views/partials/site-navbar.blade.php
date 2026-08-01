@php

  require resource_path('views/partials/site-navbar/data/context.php');
  require resource_path('views/partials/site-navbar/data/menu.php');
  require resource_path('views/partials/site-navbar/data/presentation.php');

@endphp

@once
  @vite([
    'resources/css/pages/welcome-navigation.css',
    'resources/css/pages/welcome-mega-menu.css',
  ])
@endonce

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
