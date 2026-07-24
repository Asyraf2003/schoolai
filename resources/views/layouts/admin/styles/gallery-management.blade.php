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
