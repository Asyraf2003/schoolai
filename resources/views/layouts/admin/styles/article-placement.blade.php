    .article-placement-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 16px;
      margin-bottom: 18px;
    }

    .article-placement-panel {
      min-width: 0;
      padding: 18px;
      border: 1px solid var(--admin-line);
      border-radius: 16px;
      background: #fff;
      box-shadow: 0 10px 24px rgba(32,34,63,0.06);
    }

    .article-placement-panel__head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 14px;
    }

    .article-placement-panel__head h2,
    .article-placement-panel__head p {
      margin: 0;
    }

    .article-placement-panel__head h2 {
      margin-top: 3px;
      font-size: 1.2rem;
    }

    .article-placement-panel__head p {
      max-width: 58ch;
      margin-top: 5px;
      color: var(--admin-muted);
      font-size: 0.86rem;
      line-height: 1.45;
    }

    .article-placement-panel__eyebrow {
      color: var(--admin-muted);
      font-size: 0.72rem;
      font-weight: 900;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .article-placement-panel__count {
      flex: 0 0 auto;
      display: grid;
      place-items: center;
      min-width: 48px;
      min-height: 36px;
      padding: 6px 10px;
      border-radius: 999px;
      background: #f3f4f6;
      font-size: 0.82rem;
    }

    .article-placement-list,
    .article-placement-auto {
      display: grid;
      gap: 8px;
    }

    .article-placement-auto {
      margin-top: 8px;
    }

    .article-placement-item {
      display: grid;
      grid-template-columns: 28px 30px minmax(0, 1fr) auto auto;
      align-items: center;
      gap: 8px;
      min-height: 54px;
      padding: 8px 10px;
      border: 1px solid var(--admin-line);
      border-radius: 12px;
      background: #fff;
      transition: opacity 120ms ease, transform 120ms ease, box-shadow 120ms ease;
    }

    .article-placement-item.is-dragging {
      opacity: 0.58;
      transform: scale(0.995);
      box-shadow: 0 12px 24px rgba(32,34,63,0.1);
    }

    .article-placement-item--auto {
      grid-template-columns: 28px 30px minmax(0, 1fr) auto;
      background: #fafafa;
      border-style: dashed;
    }

    .article-placement-item--fixed {
      grid-template-columns: 28px 30px minmax(0, 1fr) auto;
      margin-bottom: 8px;
      background: #f7f7f5;
    }

    .article-placement-handle {
      display: grid;
      place-items: center;
      width: 28px;
      height: 32px;
      padding: 0;
      border: 0;
      background: transparent;
      color: var(--admin-muted);
      cursor: grab;
      font: inherit;
      font-weight: 900;
      letter-spacing: -0.2em;
    }

    button.article-placement-handle:active { cursor: grabbing; }

    .article-placement-position {
      display: grid;
      place-items: center;
      width: 30px;
      height: 30px;
      border-radius: 9px;
      background: #f3f4f6;
      color: var(--admin-muted);
      font-size: 0.76rem;
      font-weight: 900;
    }

    .article-placement-copy {
      min-width: 0;
      display: grid;
      gap: 2px;
    }

    .article-placement-copy strong,
    .article-placement-copy small {
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }

    .article-placement-copy small {
      color: var(--admin-muted);
      font-size: 0.68rem;
      font-weight: 800;
      letter-spacing: 0.05em;
    }

    .article-placement-move {
      display: flex;
      gap: 4px;
    }

    .article-placement-move button {
      width: 30px;
      height: 30px;
      padding: 0;
      border: 1px solid var(--admin-line);
      border-radius: 9px;
      background: #fff;
      color: var(--admin-ink);
      cursor: pointer;
      font: inherit;
      font-weight: 900;
    }

    .article-placement-move button:disabled {
      opacity: 0.3;
      cursor: not-allowed;
    }

    .article-placement-auto__badge {
      padding: 5px 8px;
      border-radius: 999px;
      background: #eef2ff;
      color: #3730a3;
      font-size: 0.68rem;
      font-weight: 900;
    }

    .article-placement-empty {
      margin: 10px 0 0;
      padding: 12px;
      border-radius: 10px;
      background: #fafafa;
      color: var(--admin-muted);
      font-size: 0.84rem;
      line-height: 1.45;
    }

    .article-placement-order-form {
      display: flex;
      justify-content: flex-end;
      margin-top: 12px;
    }

    .article-admin-row {
      grid-template-columns: 44px minmax(0, 1fr) 96px minmax(230px, auto) minmax(270px, auto);
    }

    .article-admin-placement-actions {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 6px;
      flex-wrap: wrap;
    }

    .article-admin-placement-actions form { margin: 0; }

    .article-admin-placement-note {
      flex-basis: 100%;
      color: var(--admin-muted);
      font-size: 0.7rem;
      text-align: right;
    }

    .admin-small-action--pinned {
      background: #166534;
      color: #fff;
    }

    .admin-small-action--spotlight {
      background: #854d0e;
      color: #fff;
    }

    @media (max-width: 1320px) {
      .article-placement-grid { grid-template-columns: 1fr; }
      .article-admin-row {
        grid-template-columns: 44px minmax(0, 1fr) 96px minmax(220px, auto);
      }
      .article-admin-placement-actions {
        grid-column: 2 / -1;
        justify-content: flex-start;
      }
      .article-admin-placement-note { text-align: left; }
    }
