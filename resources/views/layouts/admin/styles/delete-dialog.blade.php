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
