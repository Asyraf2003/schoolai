@php
  $activeAdminPage = $activeAdminPage ?? ($adminPageKey ?? 'dashboard');

  $adminMenu = [
      ['key' => 'dashboard', 'label' => __('admin.nav.dashboard'), 'route' => 'admin.dashboard'],
      ['key' => 'ppdb', 'label' => __('admin.nav.ppdb'), 'route' => 'admin.ppdb'],
      ['key' => 'artikel', 'label' => __('admin.nav.artikel'), 'route' => 'admin.artikel'],
      ['key' => 'galeri', 'label' => __('admin.nav.galery'), 'route' => 'admin.galeri'],
      ['key' => 'stats', 'label' => __('admin.nav.stats'), 'route' => 'admin.stats.edit'],
  ];
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=1200, initial-scale=1">
  <title>{{ $title ?? __('admin.meta.title') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">

    @include('layouts.admin.styles.foundation')
    @include('layouts.admin.styles.topbar')
    @include('layouts.admin.styles.gallery-list')
    @include('layouts.admin.styles.gallery-detail')
    @include('layouts.admin.styles.forms')
    @include('layouts.admin.styles.notifications')
    @include('layouts.admin.styles.desktop-shell')
    @include('layouts.admin.styles.gallery-management')
    @include('layouts.admin.styles.media-detail')
    @include('layouts.admin.styles.statistics')
    @include('layouts.admin.styles.delete-dialog')

  </style>
</head>
<body class="admin-desktop-body">
  <div class="admin-toast-stack" data-admin-toast-stack aria-live="polite" aria-atomic="true"></div>
  <div
    class="admin-delete-modal"
    data-admin-delete-modal
    role="dialog"
    aria-modal="true"
    aria-labelledby="admin-delete-modal-title"
    aria-describedby="admin-delete-modal-description"
    hidden
  >
    <button type="button" class="admin-delete-modal__backdrop" data-admin-delete-cancel aria-label="Tutup modal hapus"></button>

    <section class="admin-delete-modal__panel">
      <span class="admin-delete-modal__label">Konfirmasi hapus</span>
      <h2 id="admin-delete-modal-title">Hapus data?</h2>
      <p id="admin-delete-modal-description" data-admin-delete-modal-message>
        Data yang dihapus tidak bisa dikembalikan.
      </p>

      <div class="admin-delete-modal__actions">
        <button type="button" class="admin-delete-modal__button admin-delete-modal__button--cancel" data-admin-delete-cancel>
          Batal
        </button>
        <button type="button" class="admin-delete-modal__button admin-delete-modal__button--danger" data-admin-delete-confirm>
          Ya, hapus
        </button>
      </div>
    </section>
  </div>
  <div class="admin-pc-only" role="status">
    <div class="admin-pc-only__box">
      <h1>{{ __('admin.desktop_only.title') }}</h1>
      <p>{{ __('admin.desktop_only.description') }}</p>
    </div>
  </div>

  <div class="admin-desktop-shell">
    <aside class="admin-desktop-sidebar">
      <p class="admin-sidebar-label">{{ __('admin.nav.label') }}</p>

      <nav class="admin-side-nav" aria-label="{{ __('admin.nav.label') }}">
        @foreach ($adminMenu as $item)
          <a
            href="{{ route($item['route']) }}"
            class="admin-side-link {{ $activeAdminPage === $item['key'] ? 'is-active' : '' }}"
            @if ($activeAdminPage === $item['key']) aria-current="page" @endif
          >
            <span>{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>

      <div class="admin-sidebar-bottom">
        <a href="{{ route('home') }}" class="admin-side-site">
          <span>{{ __('admin.nav.view_site') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="admin-side-logout">
            <span>{{ __('admin.nav.logout') }}</span>
          </button>
        </form>
      </div>
    </aside>

    <main class="admin-main">
      @yield('content')
    </main>
  </div>

  @include('layouts.admin.scripts.notifications')

  @include('layouts.admin.scripts.delete-dialog')

</body>
</html>

