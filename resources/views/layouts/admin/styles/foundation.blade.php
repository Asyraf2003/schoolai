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
