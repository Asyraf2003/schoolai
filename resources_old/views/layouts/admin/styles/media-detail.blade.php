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



