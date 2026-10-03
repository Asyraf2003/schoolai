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
