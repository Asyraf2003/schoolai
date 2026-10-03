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
