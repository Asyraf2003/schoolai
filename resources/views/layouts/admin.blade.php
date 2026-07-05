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
    .admin-topbar--compact { margin-bottom: 18px; }

    .admin-inline-actions {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
      justify-content: flex-end;
    }

    .admin-counter,
    .admin-primary-action,
    .admin-small-action {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: 12px;
      font-weight: 800;
      text-decoration: none;
      border: 0;
      cursor: pointer;
      font: inherit;
    }

    .admin-counter {
      min-height: 38px;
      padding: 8px 12px;
      background: #fff;
      color: var(--admin-muted);
      border: 1px solid var(--admin-line);
    }

    .admin-primary-action {
      min-height: 38px;
      padding: 8px 14px;
      background: var(--admin-dark);
      color: #fff;
    }

    .admin-primary-action--ghost {
      background: #fff;
      color: var(--admin-ink);
      border: 1px solid var(--admin-line);
    }

    .admin-small-action {
      min-height: 34px;
      padding: 7px 11px;
      background: var(--admin-dark);
      color: #fff;
      font-size: 0.86rem;
    }

    .admin-small-action--ghost {
      background: #fff;
      color: var(--admin-ink);
      border: 1px solid var(--admin-line);
    }

    .admin-small-action--danger { background: #b91c1c; }

    .admin-small-action:disabled {
      opacity: 0.4;
      cursor: not-allowed;
    }

    .admin-error-box {
      margin: 0 0 14px;
      padding: 12px 14px;
      border-radius: 12px;
      background: #fef2f2;
      color: #b91c1c;
      font-weight: 700;
    }

    .admin-error-box p { margin: 0; }

    .gallery-lite-panel,
    .gallery-detail-panel,
    .gallery-lite-form__panel,
    .gallery-media-review {
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 10px 24px rgba(32,34,63,0.06);
    }

    .gallery-lite-list { display: grid; }

    .gallery-lite-row {
      display: grid;
      grid-template-columns: 44px minmax(0, 1fr) 96px minmax(320px, auto);
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      color: var(--admin-ink);
      border-bottom: 1px solid var(--admin-line);
    }

    .gallery-lite-row:last-child { border-bottom: 0; }
    .gallery-lite-row:hover { background: #fffdf8; }
    .gallery-lite-row__order { color: var(--admin-muted); font-weight: 900; }

    .gallery-lite-row__body {
      min-width: 0;
      display: grid;
      gap: 3px;
    }

    .gallery-lite-row__body strong,
    .gallery-lite-row__body small {
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .gallery-lite-row__body small { color: var(--admin-muted); }

    .gallery-lite-status {
      justify-self: end;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 0.78rem;
      font-weight: 900;
    }

    .gallery-lite-status.is-active { background: #dcfce7; color: #166534; }
    .gallery-lite-status.is-inactive { background: #f3f4f6; color: #4b5563; }

    .gallery-lite-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      flex-wrap: wrap;
    }

    .gallery-lite-actions form {
      margin: 0;
    }

    .gallery-lite-empty { padding: 22px; }
    .gallery-lite-empty h2,
    .gallery-lite-empty p { margin: 0; }
    .gallery-lite-empty p { margin-top: 6px; color: var(--admin-muted); }

    .gallery-detail-panel {
      display: grid;
      grid-template-columns: minmax(240px, 320px) minmax(0, 1fr);
      gap: 18px;
      padding: 18px;
    }

    .gallery-detail-preview,
    .gallery-media-review__stage {
      min-height: 220px;
      display: grid;
      place-items: center;
      border-radius: 14px;
      background: #f6f3ec;
      overflow: hidden;
      color: var(--admin-muted);
      font-weight: 900;
    }

    .gallery-detail-preview img,
    .gallery-detail-preview iframe,
    .gallery-media-review__stage img,
    .gallery-media-review__stage iframe {
      width: 100%;
      height: 100%;
      min-height: 220px;
      border: 0;
      object-fit: cover;
      display: block;
      background: #111827;
    }

    .gallery-detail-list {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 10px 18px;
      margin: 0;
    }

    .gallery-detail-list div {
      display: grid;
      gap: 4px;
      padding-bottom: 8px;
      border-bottom: 1px dashed var(--admin-line);
    }

    .gallery-detail-list__wide { grid-column: 1 / -1; }

    .gallery-detail-list dt {
      color: var(--admin-muted);
      font-size: 0.78rem;
      font-weight: 900;
      text-transform: uppercase;
    }

    .gallery-detail-list dd {
      margin: 0;
      color: var(--admin-ink);
      word-break: break-word;
    }

    .gallery-detail-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 14px;
    }

    .gallery-detail-actions form { margin: 0; }

    .gallery-lite-form {
      display: grid;
      gap: 14px;
    }

    .gallery-lite-form__panel,
    .gallery-media-review {
      padding: 18px;
    }

    .gallery-lite-form__grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .admin-field {
      display: grid;
      gap: 6px;
    }

    .admin-field--wide { grid-column: 1 / -1; }

    .admin-field label,
    .admin-check-field { font-weight: 800; }

    .admin-field input,
    .admin-field select,
    .admin-field textarea {
      width: 100%;
      border: 1px solid var(--admin-line);
      border-radius: 12px;
      padding: 10px 12px;
      background: #fffdf8;
      color: var(--admin-ink);
      font: inherit;
      outline: none;
    }

    .admin-field input[type="file"] { background: #fff; }
    .admin-field textarea { resize: vertical; }

    .admin-field input:focus,
    .admin-field select:focus,
    .admin-field textarea:focus {
      border-color: var(--admin-orange);
      box-shadow: 0 0 0 3px rgba(249,115,22,0.12);
    }

    .admin-field small {
      color: #b91c1c;
      font-weight: 700;
    }

    .admin-field em {
      color: var(--admin-muted);
      font-size: 0.82rem;
      font-style: normal;
      line-height: 1.45;
      word-break: break-word;
    }

    .admin-check-field {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 11px 12px;
      border: 1px solid var(--admin-line);
      border-radius: 12px;
      background: #fffdf8;
    }

    .admin-check-field input {
      width: 17px;
      height: 17px;
    }

    .gallery-media-review__head {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      margin-bottom: 12px;
    }

    @media (max-width: 980px) {
      .gallery-detail-panel {
        grid-template-columns: 1fr;
      }

      .gallery-lite-form__grid,
      .gallery-detail-list {
        grid-template-columns: 1fr;
      }

      .admin-topbar--compact,
      .admin-inline-actions {
        align-items: stretch;
      }

      .admin-inline-actions {
        justify-content: flex-start;
      }
    }

    @media (max-width: 640px) {
      .gallery-lite-row {
        grid-template-columns: 34px minmax(0, 1fr);
      }

      .gallery-lite-status,
      .gallery-lite-actions {
        grid-column: 2;
        justify-self: start;
      }

      .gallery-lite-actions {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .admin-primary-action,
      .admin-small-action {
        width: 100%;
      }

      .gallery-detail-actions {
        display: grid;
      }
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
