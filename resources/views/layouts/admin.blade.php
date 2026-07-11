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

    .gallery-media-review__stage.is-landscape,
    .gallery-media-review__stage.is-portrait,
    .gallery-media-review__stage.is-square {
      width: 100%;
      min-height: 0;
      margin-inline: auto;
      background: #111827;
    }

    .gallery-media-review__stage.is-landscape {
      aspect-ratio: 16 / 9;
    }

    .gallery-media-review__stage.is-portrait {
      width: min(100%, 360px);
      aspect-ratio: 9 / 16;
    }

    .gallery-media-review__stage.is-square {
      width: min(100%, 560px);
      aspect-ratio: 1;
    }

    .gallery-media-review__stage.is-landscape iframe,
    .gallery-media-review__stage.is-portrait iframe,
    .gallery-media-review__stage.is-square iframe {
      width: 100%;
      height: 100%;
      min-height: 0;
      object-fit: contain;
    }

    .gallery-media-review__stage.is-image img {
      width: auto;
      height: auto;
      max-width: 100%;
      max-height: 620px;
      object-fit: contain;
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

    .admin-toast-stack {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 2000;
      display: grid;
      gap: 10px;
      width: min(380px, calc(100vw - 32px));
      pointer-events: none;
    }

    body.admin-desktop-body .flash-message,
    body.admin-desktop-body .admin-error-box {
      position: fixed;
      top: 24px;
      right: 24px;
      z-index: 2000;
      width: min(380px, calc(100vw - 32px));
      margin: 0;
      padding: 14px 44px 14px 16px;
      border-radius: 16px;
      font-weight: 800;
      line-height: 1.45;
      box-shadow: 0 18px 42px rgba(32, 34, 63, 0.18);
      pointer-events: auto;
    }

    .admin-toast-stack .flash-message,
    .admin-toast-stack .admin-error-box,
    .admin-toast {
      position: relative;
      inset: auto;
      width: 100%;
      margin: 0;
      transform-origin: top right;
      animation: admin-toast-in 0.24s ease-out both;
    }

    .admin-toast--success,
    .flash-message {
      border: 1px solid rgba(22, 163, 74, 0.18);
      background: #ecfdf5;
      color: #166534;
    }

    .admin-toast--error,
    .admin-error-box {
      border: 1px solid rgba(185, 28, 28, 0.18);
      background: #fef2f2;
      color: #b91c1c;
    }

    .admin-toast p,
    .admin-error-box p {
      margin: 0;
    }

    .admin-toast p + p,
    .admin-error-box p + p {
      margin-top: 4px;
    }

    .admin-toast__close {
      position: absolute;
      top: 9px;
      right: 10px;
      width: 26px;
      height: 26px;
      display: grid;
      place-items: center;
      border: 0;
      border-radius: 999px;
      background: rgba(32, 34, 63, 0.08);
      color: currentColor;
      font: inherit;
      font-size: 1rem;
      font-weight: 900;
      line-height: 1;
      cursor: pointer;
    }

    .admin-toast__close:hover {
      background: rgba(32, 34, 63, 0.14);
    }

    .admin-toast.is-hiding {
      animation: admin-toast-out 0.22s ease-in both;
    }

    @keyframes admin-toast-in {
      from {
        opacity: 0;
        transform: translateY(-10px) translateX(10px) scale(0.98);
      }

      to {
        opacity: 1;
        transform: translateY(0) translateX(0) scale(1);
      }
    }

    @keyframes admin-toast-out {
      from {
        opacity: 1;
        transform: translateY(0) translateX(0) scale(1);
      }

      to {
        opacity: 0;
        transform: translateY(-8px) translateX(10px) scale(0.98);
      }
    }

    @media (max-width: 640px) {
      .admin-toast-stack,
      body.admin-desktop-body .flash-message,
      body.admin-desktop-body .admin-error-box {
        top: 14px;
        right: 14px;
        left: 14px;
        width: auto;
      }
    }

    body.admin-desktop-body {
      background: #f6f7f9;
      color: #111827;
    }

    .admin-desktop-shell {
      grid-template-columns: 220px minmax(860px, 1fr);
    }

    .admin-desktop-sidebar {
      padding: 22px 14px;
      background: #111827;
      color: #ffffff;
    }

    .admin-sidebar-label {
      margin: 0 0 14px;
      padding: 0 10px;
      color: #9ca3af;
      font-size: 0.74rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .admin-side-nav {
      gap: 6px;
    }

    .admin-side-link,
    .admin-side-logout,
    .admin-side-site {
      min-height: 42px;
      padding: 10px 12px;
      border-radius: 10px;
      color: #d1d5db;
      background: transparent;
      font-weight: 760;
      transform: none;
    }

    .admin-side-link:hover,
    .admin-side-logout:hover,
    .admin-side-site:hover {
      color: #ffffff;
      background: #1f2937;
      transform: none;
    }

    .admin-side-link.is-active {
      color: #111827;
      background: #ffffff;
      box-shadow: none;
    }

    .admin-sidebar-bottom {
      gap: 6px;
      padding-top: 14px;
      border-top: 1px solid rgba(255,255,255,0.10);
    }

    .admin-main {
      padding: 28px 32px 40px;
    }

    .admin-topbar h1 {
      font-size: clamp(1.6rem, 2vw, 2.1rem);
      line-height: 1.15;
      letter-spacing: -0.03em;
    }

    .admin-content-panel {
      border-radius: 16px;
      background: #ffffff;
      box-shadow: none;
    }

    .admin-simple-page {
      max-width: 720px;
      padding: 28px;
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      background: #ffffff;
    }

    .admin-simple-page h1 {
      margin: 0;
      color: #111827;
      font-size: 1.8rem;
      line-height: 1.2;
      letter-spacing: -0.03em;
    }

    .admin-simple-page p {
      margin: 10px 0 0;
      color: #4b5563;
      font-size: 1rem;
      line-height: 1.6;
    }

    .admin-gallery-block {
      display: grid;
      gap: 16px;
      margin-bottom: 18px;
      padding: 18px;
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      background: #ffffff;
      box-shadow: none;
    }

    .admin-gallery-block__head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
      padding-bottom: 14px;
      border-bottom: 1px solid var(--admin-line);
    }

    .admin-gallery-block__head h2,
    .admin-section-card h3,
    .admin-media-card h3 {
      margin: 0;
      color: #111827;
      line-height: 1.18;
      letter-spacing: -0.025em;
    }

    .admin-gallery-block__head p,
    .admin-section-card p,
    .admin-media-card p {
      margin: 7px 0 0;
      color: var(--admin-muted);
      line-height: 1.55;
    }

    .admin-section-grid,
    .admin-media-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .admin-section-card,
    .admin-media-card {
      min-width: 0;
      display: grid;
      gap: 14px;
      padding: 16px;
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      background: #fffdf8;
    }

    .admin-section-card__body,
    .admin-media-card__body {
      min-width: 0;
      display: grid;
      gap: 8px;
    }

    .admin-section-card__body small {
      color: var(--admin-muted);
      font-weight: 850;
    }

    .admin-section-card__actions {
      justify-content: flex-start;
      padding-top: 12px;
      border-top: 1px solid var(--admin-line);
    }

    .admin-media-card__preview {
      min-height: 180px;
      display: grid;
      place-items: center;
      border-radius: 14px;
      background: #f3f1ec;
      overflow: hidden;
      color: var(--admin-muted);
      font-weight: 900;
    }

    .admin-media-card__preview img,
    .admin-media-card__preview iframe {
      width: 100%;
      height: 100%;
      min-height: 180px;
      border: 0;
      object-fit: cover;
      display: block;
      background: #111827;
    }

    .admin-primary-action--danger {
      background: #b91c1c;
      color: #ffffff;
    }

    @media (max-width: 980px) {
      .admin-gallery-block__head {
        display: grid;
      }

      .admin-section-grid,
      .admin-media-grid {
        grid-template-columns: 1fr;
      }
    }

    .gallery-media-review__head small {
      color: var(--admin-muted);
      font-size: 0.84rem;
      font-weight: 800;
    }

    .gallery-media-review__multi {
      width: 100%;
      min-height: 220px;
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 10px;
      padding: 10px;
    }

    .gallery-media-review__multi img,
    .gallery-media-review__multi iframe {
      width: 100%;
      height: 150px;
      min-height: 150px;
      border: 0;
      border-radius: 10px;
      object-fit: cover;
      background: #111827;
    }

    .gallery-media-review__multi iframe.gallery-media-review__video {
      height: auto;
      min-height: 0;
      object-fit: contain;
      align-self: start;
    }

    .gallery-media-review__multi iframe.gallery-media-review__video.is-landscape {
      aspect-ratio: 16 / 9;
    }

    .gallery-media-review__multi iframe.gallery-media-review__video.is-portrait {
      width: min(100%, 220px);
      aspect-ratio: 9 / 16;
      justify-self: center;
    }

    .gallery-media-review__multi iframe.gallery-media-review__video.is-square {
      aspect-ratio: 1;
    }

    @media (max-width: 980px) {
      .gallery-media-review__multi {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    .admin-gallery-toolbar {
      display: flex;
      justify-content: flex-end;
      margin-bottom: 14px;
    }

    .gallery-detail-panel--simple {
      grid-template-columns: minmax(420px, 0.95fr) minmax(460px, 1.05fr);
      align-items: stretch;
      gap: 22px;
      padding: 22px;
      border-radius: 16px;
      background: #ffffff;
      box-shadow: none;
    }

    .gallery-detail-preview--large {
      min-height: 430px;
      border-radius: 14px;
      background: #f3f1ec;
    }

    .gallery-detail-preview--large img,
    .gallery-detail-preview--large iframe {
      min-height: 430px;
    }

    .gallery-detail-summary {
      min-width: 0;
      display: grid;
      align-content: start;
      gap: 18px;
    }

    .gallery-detail-summary__head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
      padding-bottom: 16px;
      border-bottom: 1px solid var(--admin-line);
    }

    .gallery-detail-summary__head h1 {
      margin: 0;
      color: #111827;
      font-size: clamp(1.7rem, 2vw, 2.3rem);
      line-height: 1.15;
      letter-spacing: -0.035em;
    }

    .gallery-detail-summary__head p {
      margin: 8px 0 0;
      color: var(--admin-muted);
      line-height: 1.5;
    }

    .gallery-detail-list--simple {
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 12px 20px;
    }

    .gallery-detail-list--simple div {
      padding-bottom: 10px;
    }

    .gallery-detail-actions--simple {
      margin-top: 0;
      padding-top: 16px;
      border-top: 1px solid var(--admin-line);
    }

    @media (max-width: 980px) {
      .gallery-detail-panel--simple {
        grid-template-columns: 1fr;
      }

      .gallery-detail-summary__head {
        display: grid;
      }
    }


    .admin-delete-modal[hidden] {
      display: none;
    }

    .admin-delete-modal {
      position: fixed;
      inset: 0;
      z-index: 2000;
      display: grid;
      place-items: center;
      padding: 28px;
    }

    .admin-delete-modal__backdrop {
      position: absolute;
      inset: 0;
      border: 0;
      background: rgba(15, 23, 42, 0.48);
      cursor: pointer;
    }

    .admin-delete-modal__panel {
      position: relative;
      z-index: 1;
      width: min(420px, 100%);
      padding: 24px;
      border: 1px solid rgba(185, 28, 28, 0.16);
      border-radius: 24px;
      background: #ffffff;
      box-shadow: 0 28px 80px rgba(15, 23, 42, 0.22);
    }

    .admin-delete-modal__label {
      display: inline-flex;
      margin-bottom: 10px;
      padding: 6px 10px;
      border-radius: 999px;
      background: #fef2f2;
      color: #b91c1c;
      font-size: 0.78rem;
      font-weight: 950;
    }

    .admin-delete-modal__panel h2 {
      margin: 0;
      color: var(--admin-ink);
      font-size: 1.45rem;
      line-height: 1.16;
      letter-spacing: -0.035em;
    }

    .admin-delete-modal__panel p {
      margin: 10px 0 0;
      color: var(--admin-muted);
      line-height: 1.6;
      font-weight: 720;
    }

    .admin-delete-modal__actions {
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      margin-top: 22px;
    }

    .admin-delete-modal__button {
      min-height: 40px;
      padding: 9px 14px;
      border-radius: 12px;
      border: 0;
      font: inherit;
      font-weight: 900;
      cursor: pointer;
    }

    .admin-delete-modal__button--cancel {
      background: #ffffff;
      color: var(--admin-ink);
      border: 1px solid var(--admin-line);
    }

    .admin-delete-modal__button--danger {
      background: #b91c1c;
      color: #ffffff;
    }

    .admin-delete-modal__button:focus-visible,
    .admin-delete-modal__backdrop:focus-visible {
      outline: 3px solid rgba(185, 28, 28, 0.28);
      outline-offset: 3px;
    }

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
  <script>
    (() => {
      const stack = document.querySelector('[data-admin-toast-stack]');
      if (!stack) return;

      const messages = Array.from(document.querySelectorAll('.flash-message, .admin-error-box'));

      function hideToast(toast) {
        if (!toast || toast.classList.contains('is-hiding')) return;

        toast.classList.add('is-hiding');

        window.setTimeout(() => {
          toast.remove();
        }, 240);
      }

      messages.forEach((toast, index) => {
        if (toast.closest('[data-admin-toast-stack]')) return;

        const isError = toast.classList.contains('admin-error-box');

        toast.classList.add('admin-toast', isError ? 'admin-toast--error' : 'admin-toast--success');
        toast.setAttribute('role', isError ? 'alert' : 'status');
        toast.setAttribute('data-admin-toast', '');

        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'admin-toast__close';
        closeButton.setAttribute('aria-label', 'Tutup notifikasi');
        closeButton.textContent = '×';
        closeButton.addEventListener('click', () => hideToast(toast));

        toast.appendChild(closeButton);
        stack.appendChild(toast);

        window.setTimeout(() => hideToast(toast), 3000 + (index * 120));
      });
    })();
  </script>
  <script>
    (() => {
      const modal = document.querySelector('[data-admin-delete-modal]');
      if (!modal) return;

      const messageEl = modal.querySelector('[data-admin-delete-modal-message]');
      const confirmButton = modal.querySelector('[data-admin-delete-confirm]');
      const cancelButtons = Array.from(modal.querySelectorAll('[data-admin-delete-cancel]'));
      const deleteForms = Array.from(document.querySelectorAll('[data-admin-delete-form]'));

      let pendingForm = null;
      let lastFocused = null;

      function openDeleteModal(form) {
        pendingForm = form;
        lastFocused = document.activeElement;

        if (messageEl) {
          messageEl.textContent = form.getAttribute('data-admin-delete-message') || 'Data yang dihapus tidak bisa dikembalikan.';
        }

        modal.hidden = false;

        window.requestAnimationFrame(() => {
          if (confirmButton) confirmButton.focus();
        });
      }

      function closeDeleteModal() {
        modal.hidden = true;
        pendingForm = null;

        if (lastFocused && typeof lastFocused.focus === 'function') {
          lastFocused.focus();
        }

        lastFocused = null;
      }

      deleteForms.forEach((form) => {
        const trigger = form.querySelector('[data-admin-delete-trigger]') || form.querySelector('button');

        if (trigger) {
          trigger.type = 'button';
          trigger.setAttribute('data-admin-delete-trigger', '');
          trigger.addEventListener('click', () => openDeleteModal(form));
        }

        form.addEventListener('submit', (event) => {
          if (form.getAttribute('data-admin-delete-confirmed') === '1') {
            return;
          }

          event.preventDefault();
          openDeleteModal(form);
        });
      });

      cancelButtons.forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
      });

      if (confirmButton) {
        confirmButton.addEventListener('click', () => {
          if (!pendingForm) return;

          pendingForm.setAttribute('data-admin-delete-confirmed', '1');

          if (typeof pendingForm.requestSubmit === 'function') {
            pendingForm.requestSubmit();
            return;
          }

          pendingForm.submit();
        });
      }

      document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
          closeDeleteModal();
        }
      });
    })();
  </script>
</body>
</html>
