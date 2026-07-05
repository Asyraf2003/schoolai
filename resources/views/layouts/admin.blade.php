{{-- ADMIN_DESKTOP_DUMMY_LAYOUT_FINAL --}}
@php
  $activeAdminPage = $activeAdminPage ?? ($adminPageKey ?? 'dashboard');

  $adminMenu = [
      ['key' => 'dashboard', 'label' => __('admin.nav.dashboard'), 'route' => 'admin.dashboard'],
      ['key' => 'ppdb', 'label' => __('admin.nav.ppdb'), 'route' => 'admin.ppdb'],
      ['key' => 'artikel', 'label' => __('admin.nav.artikel'), 'route' => 'admin.artikel'],
      ['key' => 'galeri', 'label' => __('admin.nav.galery'), 'route' => 'admin.galeri'],
  ];
@endphp

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=1200, initial-scale=1">
  <title>{{ $title ?? __('admin.meta.title') }}</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    /* ADMIN_DESKTOP_DUMMY_FINAL */
    :root {
      color-scheme: light;
      --admin-ink: #20223f;
      --admin-muted: #73788b;
      --admin-bg: #f6f3ec;
      --admin-panel: #ffffff;
      --admin-line: #e7dfd3;
      --admin-dark: #201f3d;
      --admin-dark-soft: #2b2a50;
      --admin-orange: #f97316;
      --admin-yellow: #fff2c6;
      --admin-blue: #19aee6;
      --admin-green: #16a34a;
      --admin-radius: 26px;
      --admin-shadow: 0 22px 60px rgba(32, 34, 63, 0.10);
    }

    * {
      box-sizing: border-box;
    }

    body.admin-desktop-body {
      margin: 0;
      min-height: 100vh;
      overflow-x: auto;
      background:
        radial-gradient(circle at 8% 10%, rgba(25, 174, 230, 0.10), transparent 24%),
        radial-gradient(circle at 96% 4%, rgba(249, 115, 22, 0.10), transparent 26%),
        var(--admin-bg);
      color: var(--admin-ink);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .admin-pc-only {
      display: none;
      min-height: 100vh;
      place-items: center;
      padding: 28px;
      text-align: center;
      background: var(--admin-bg);
    }

    .admin-pc-only__box {
      max-width: 520px;
      padding: 28px;
      border-radius: 28px;
      background: var(--admin-panel);
      box-shadow: var(--admin-shadow);
    }

    .admin-pc-only__box h1 {
      margin: 0 0 10px;
      font-size: 1.7rem;
    }

    .admin-pc-only__box p {
      margin: 0;
      color: var(--admin-muted);
      line-height: 1.7;
    }

    .admin-desktop-shell {
      min-width: 1180px;
      min-height: 100vh;
      display: grid;
      grid-template-columns: 286px minmax(860px, 1fr);
    }

    .admin-desktop-sidebar {
      position: sticky;
      top: 0;
      height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 28px 22px;
      background:
        radial-gradient(circle at 30% 10%, rgba(255, 242, 198, 0.14), transparent 26%),
        linear-gradient(180deg, var(--admin-dark), #151528);
      color: #fff;
    }

    .admin-brand {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 34px;
    }

    .admin-brand__mark {
      width: 48px;
      height: 48px;
      display: grid;
      place-items: center;
      border-radius: 17px;
      background: var(--admin-yellow);
      color: var(--admin-dark);
      font-weight: 950;
      box-shadow: 0 16px 34px rgba(0,0,0,0.18);
    }

    .admin-brand strong {
      display: block;
      line-height: 1.05;
      font-size: 1.02rem;
    }

    .admin-brand small {
      color: rgba(255,255,255,0.62);
      font-weight: 700;
    }

    .admin-sidebar-label {
      margin: 0 0 12px;
      color: rgba(255,255,255,0.44);
      font-size: 0.76rem;
      font-weight: 900;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .admin-side-nav {
      display: grid;
      gap: 10px;
    }

    .admin-side-link,
    .admin-side-logout,
    .admin-side-site {
      display: flex;
      align-items: center;
      gap: 11px;
      width: 100%;
      min-height: 48px;
      padding: 12px 14px;
      border: 0;
      border-radius: 16px;
      color: rgba(255,255,255,0.76);
      background: transparent;
      text-decoration: none;
      text-align: left;
      font: inherit;
      font-weight: 850;
      cursor: pointer;
      transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .admin-side-link:hover,
    .admin-side-logout:hover,
    .admin-side-site:hover {
      color: #fff;
      background: rgba(255,255,255,0.10);
      transform: translateX(2px);
    }

    .admin-side-link.is-active {
      color: var(--admin-dark);
      background: var(--admin-yellow);
      box-shadow: 0 14px 30px rgba(255, 242, 198, 0.14);
    }

    .admin-side-link__icon,
    .admin-side-site__icon {
      width: 28px;
      height: 28px;
      display: grid;
      place-items: center;
      border-radius: 10px;
      background: rgba(255,255,255,0.12);
      font-size: 0.95rem;
    }

    .admin-side-link.is-active .admin-side-link__icon {
      background: rgba(32,31,61,0.10);
    }

    .admin-sidebar-bottom {
      display: grid;
      gap: 10px;
      margin-top: auto;
      padding-top: 20px;
      border-top: 1px solid rgba(255,255,255,0.10);
    }

    .admin-main {
      min-width: 860px;
      padding: 34px 38px 46px;
    }

    .admin-topbar {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 22px;
      margin-bottom: 24px;
    }

    .admin-topbar__eyebrow {
      margin: 0 0 8px;
      color: var(--admin-orange);
      font-size: 0.82rem;
      font-weight: 950;
      letter-spacing: 0.12em;
      text-transform: uppercase;
    }

    .admin-topbar h1 {
      margin: 0;
      font-size: clamp(2rem, 3vw, 3.2rem);
      line-height: 0.98;
      letter-spacing: -0.055em;
    }

    .admin-topbar p {
      max-width: 720px;
      margin: 12px 0 0;
      color: var(--admin-muted);
      line-height: 1.7;
    }

    .admin-status-pill {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      white-space: nowrap;
      padding: 10px 14px;
      border-radius: 999px;
      background: #fff;
      color: var(--admin-green);
      font-weight: 950;
      box-shadow: 0 14px 34px rgba(32, 34, 63, 0.08);
    }

    .admin-status-pill::before {
      content: "";
      width: 9px;
      height: 9px;
      border-radius: 999px;
      background: var(--admin-green);
      box-shadow: 0 0 0 5px rgba(22, 163, 74, 0.12);
    }

    .admin-notice {
      margin: 0 0 24px;
      padding: 15px 18px;
      border: 1px solid rgba(249, 115, 22, 0.18);
      border-radius: 20px;
      background: rgba(255, 247, 237, 0.84);
      color: #9a4b13;
      font-weight: 780;
    }

    .admin-content-panel {
      border: 1px solid var(--admin-line);
      border-radius: 34px;
      background:
        radial-gradient(circle at 90% 10%, rgba(25,174,230,0.10), transparent 24%),
        var(--admin-panel);
      box-shadow: var(--admin-shadow);
      overflow: hidden;
    }

    .admin-empty-hero {
      display: grid;
      grid-template-columns: 1fr 280px;
      gap: 28px;
      padding: 34px;
      align-items: center;
    }

    .admin-empty-hero h2 {
      margin: 0;
      font-size: clamp(1.7rem, 2.6vw, 2.6rem);
      line-height: 1.08;
      letter-spacing: -0.04em;
    }

    .admin-empty-hero p {
      margin: 12px 0 0;
      color: var(--admin-muted);
      line-height: 1.75;
    }

    .admin-empty-visual {
      min-height: 220px;
      display: grid;
      place-items: center;
      border-radius: 28px;
      background:
        radial-gradient(circle at 20% 20%, rgba(255,255,255,0.66), transparent 26%),
        linear-gradient(135deg, #e0f7ff, #fff2c6);
      font-size: 4.4rem;
    }

    .admin-card-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
      padding: 0 34px 34px;
    }

    .admin-dummy-card {
      min-height: 138px;
      padding: 20px;
      border: 1px solid var(--admin-line);
      border-radius: 24px;
      background: #fffdf8;
    }

    .admin-dummy-card span {
      display: block;
      color: var(--admin-muted);
      font-size: 0.82rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .admin-dummy-card strong {
      display: block;
      margin-top: 12px;
      color: var(--admin-ink);
      font-size: 1.18rem;
      line-height: 1.25;
    }

    @media (max-width: 1023px) {
      body.admin-desktop-body {
        overflow: hidden;
      }

      .admin-pc-only {
        display: grid;
      }

      .admin-desktop-shell {
        display: none;
      }
    }

    /* ADMIN_GALLERY_DUMMY_FINAL */
    .admin-primary-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 44px;
      padding: 12px 18px;
      border-radius: 999px;
      background: linear-gradient(135deg, var(--admin-orange), #ea580c);
      color: #fff;
      text-decoration: none;
      font-weight: 950;
      border: 0;
      box-shadow: 0 16px 34px rgba(234,88,12,0.22);
    }

    .admin-primary-action--ghost {
      background: #fff;
      color: var(--admin-ink);
      box-shadow: 0 14px 34px rgba(32,34,63,0.08);
    }

    .admin-error-box {
      margin: 0 0 18px;
      padding: 14px 16px;
      border-radius: 18px;
      background: #fef2f2;
      color: #b91c1c;
      font-weight: 800;
    }

    .admin-error-box p {
      margin: 0;
    }

    .admin-error-box p + p {
      margin-top: 6px;
    }

    .gallery-admin-summary {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 16px;
      margin-bottom: 24px;
    }

    .gallery-admin-summary article,
    .gallery-admin-panel,
    .gallery-form-panel {
      border: 1px solid var(--admin-line);
      background: rgba(255,255,255,0.88);
      box-shadow: var(--admin-shadow);
    }

    .gallery-admin-summary article {
      min-height: 154px;
      padding: 22px;
      border-radius: 26px;
    }

    .gallery-admin-summary span {
      display: inline-flex;
      margin-bottom: 14px;
      padding: 7px 12px;
      border-radius: 999px;
      background: rgba(25, 174, 230, 0.10);
      color: #087ca6;
      font-size: 0.78rem;
      font-weight: 950;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .gallery-admin-summary strong {
      display: block;
      color: var(--admin-ink);
      font-size: 1.12rem;
      line-height: 1.25;
    }

    .gallery-admin-summary p {
      margin: 10px 0 0;
      color: var(--admin-muted);
      line-height: 1.65;
    }

    .gallery-admin-panel,
    .gallery-form-panel {
      margin-top: 24px;
      border-radius: 32px;
      overflow: hidden;
    }

    .gallery-admin-panel__head {
      display: flex;
      justify-content: space-between;
      gap: 20px;
      align-items: flex-start;
      padding: 26px 28px;
      border-bottom: 1px solid var(--admin-line);
      background:
        radial-gradient(circle at 92% 10%, rgba(25, 174, 230, 0.10), transparent 22%),
        rgba(255,253,248,0.88);
    }

    .gallery-admin-panel__head h2 {
      margin: 0;
      color: var(--admin-ink);
      font-size: 1.45rem;
      letter-spacing: -0.02em;
    }

    .gallery-admin-panel__head p {
      max-width: 720px;
      margin: 8px 0 0;
      color: var(--admin-muted);
      line-height: 1.65;
    }

    .gallery-admin-panel__head > span {
      display: inline-flex;
      padding: 10px 14px;
      border-radius: 999px;
      background: var(--admin-dark);
      color: #fff;
      font-weight: 950;
    }

    .gallery-admin-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 18px;
      padding: 26px;
    }

    .gallery-admin-card {
      overflow: hidden;
      border: 1px solid var(--admin-line);
      border-radius: 26px;
      background: #fffdf8;
    }

    .gallery-admin-card__visual {
      position: relative;
      min-height: 170px;
      display: grid;
      place-items: center;
      background:
        radial-gradient(circle at 22% 18%, rgba(255,255,255,0.70), transparent 28%),
        linear-gradient(135deg, color-mix(in srgb, var(--admin-gallery-accent) 24%, white), #fff2c6);
      font-size: 3.7rem;
    }

    .gallery-admin-card__visual small {
      position: absolute;
      left: 14px;
      bottom: 14px;
      display: inline-flex;
      padding: 7px 11px;
      border-radius: 999px;
      background: rgba(255,255,255,0.86);
      color: var(--admin-ink);
      font-size: 0.76rem;
      font-weight: 950;
      box-shadow: 0 10px 26px rgba(32,34,63,0.10);
    }

    .gallery-admin-card__body {
      padding: 18px;
    }

    .gallery-admin-card__title-row {
      display: grid;
      gap: 10px;
    }

    .gallery-admin-card__body h3 {
      margin: 0;
      min-height: 52px;
      color: var(--admin-ink);
      font-size: 1.05rem;
      line-height: 1.25;
    }

    .gallery-status {
      display: inline-flex;
      width: fit-content;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 0.76rem;
      font-weight: 950;
    }

    .gallery-status.is-published {
      background: #dcfce7;
      color: #166534;
    }

    .gallery-status.is-draft {
      background: #f3f4f6;
      color: #4b5563;
    }

    .gallery-admin-card dl {
      display: grid;
      gap: 8px;
      margin: 16px 0 0;
    }

    .gallery-admin-card dl div {
      display: flex;
      justify-content: space-between;
      gap: 14px;
      padding-bottom: 8px;
      border-bottom: 1px dashed var(--admin-line);
    }

    .gallery-admin-card dt {
      color: var(--admin-muted);
      font-size: 0.78rem;
      font-weight: 900;
      text-transform: uppercase;
    }

    .gallery-admin-card dd {
      margin: 0;
      color: var(--admin-ink);
      font-weight: 850;
      text-align: right;
    }

    .gallery-admin-card__body p {
      margin: 14px 0 0;
      color: var(--admin-muted);
      line-height: 1.6;
    }

    .gallery-card-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-top: 16px;
    }

    .gallery-card-actions form {
      margin: 0;
    }

    .admin-small-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-height: 36px;
      padding: 8px 12px;
      border-radius: 999px;
      border: 0;
      background: var(--admin-dark);
      color: #fff;
      text-decoration: none;
      font: inherit;
      font-size: 0.85rem;
      font-weight: 900;
      cursor: pointer;
    }

    .admin-small-action--danger {
      background: #b91c1c;
    }

    .gallery-admin-empty {
      padding: 34px;
    }

    .gallery-admin-empty h3 {
      margin: 0;
      font-size: 1.35rem;
    }

    .gallery-admin-empty p {
      margin: 8px 0 0;
      color: var(--admin-muted);
    }

    .gallery-db-map {
      overflow-x: auto;
      padding: 0;
    }

    .gallery-db-map table {
      width: 100%;
      min-width: 760px;
      border-collapse: collapse;
    }

    .gallery-db-map th,
    .gallery-db-map td {
      padding: 16px 18px;
      border-bottom: 1px solid var(--admin-line);
      text-align: left;
      vertical-align: top;
    }

    .gallery-db-map th {
      background: #fffdf8;
      color: var(--admin-muted);
      font-size: 0.78rem;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .gallery-db-map code {
      display: inline-flex;
      padding: 5px 8px;
      border-radius: 9px;
      background: rgba(32,31,61,0.08);
      color: var(--admin-ink);
      font-weight: 850;
    }

    .gallery-admin-form {
      display: grid;
      gap: 20px;
    }

    .gallery-form-panel {
      padding: 28px;
    }

    .gallery-form-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 18px;
    }

    .admin-field {
      display: grid;
      gap: 8px;
    }

    .admin-field--wide {
      grid-column: 1 / -1;
    }

    .admin-field label,
    .admin-check-field {
      color: var(--admin-ink);
      font-weight: 900;
    }

    .admin-field input,
    .admin-field select,
    .admin-field textarea {
      width: 100%;
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      padding: 12px 14px;
      background: #fffdf8;
      color: var(--admin-ink);
      font: inherit;
      outline: none;
    }

    .admin-field textarea {
      resize: vertical;
    }

    .admin-field input:focus,
    .admin-field select:focus,
    .admin-field textarea:focus {
      border-color: var(--admin-orange);
      box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.13);
    }

    .admin-field small {
      color: #b91c1c;
      font-weight: 800;
    }

    .admin-field em {
      color: var(--admin-muted);
      font-size: 0.82rem;
      font-style: normal;
      line-height: 1.5;
    }

    .admin-check-field {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 14px;
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      background: #fffdf8;
    }

    .admin-check-field input {
      width: 18px;
      height: 18px;
    }
    /* /ADMIN_GALLERY_DUMMY_FINAL */

    /* /ADMIN_DESKTOP_DUMMY_FINAL */
  </style>
</head>
<body class="admin-desktop-body">
  <div class="admin-pc-only" role="status">
    <div class="admin-pc-only__box">
      <h1>{{ __('admin.desktop_only.title') }}</h1>
      <p>{{ __('admin.desktop_only.description') }}</p>
    </div>
  </div>

  <div class="admin-desktop-shell">
    <aside class="admin-desktop-sidebar">
      <div class="admin-brand">
        <span class="admin-brand__mark">{{ __('admin.brand.mark') }}</span>
        <span>
          <strong>{{ __('admin.brand.name') }}</strong>
          <small>{{ __('admin.brand.panel') }}</small>
        </span>
      </div>

      <p class="admin-sidebar-label">{{ __('admin.nav.label') }}</p>

      <nav class="admin-side-nav" aria-label="{{ __('admin.nav.label') }}">
        @foreach ($adminMenu as $item)
          <a
            href="{{ route($item['route']) }}"
            class="admin-side-link {{ $activeAdminPage === $item['key'] ? 'is-active' : '' }}"
            @if ($activeAdminPage === $item['key']) aria-current="page" @endif
          >
            <span class="admin-side-link__icon" aria-hidden="true">
              @switch($item['key'])
                @case('dashboard') ◼ @break
                @case('ppdb') ✎ @break
                @case('artikel') ¶ @break
                @case('galeri') ◇ @break
              @endswitch
            </span>
            <span>{{ $item['label'] }}</span>
          </a>
        @endforeach
      </nav>

      <div class="admin-sidebar-bottom">
        <a href="{{ route('home') }}" class="admin-side-site">
          <span class="admin-side-site__icon" aria-hidden="true">↗</span>
          <span>{{ __('admin.nav.view_site') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="admin-side-logout">
            <span class="admin-side-site__icon" aria-hidden="true">⏻</span>
            <span>{{ __('admin.nav.logout') }}</span>
          </button>
        </form>
      </div>
    </aside>

    <main class="admin-main">
      @yield('content')
    </main>
  </div>
</body>
</html>
