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
