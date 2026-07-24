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
