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
