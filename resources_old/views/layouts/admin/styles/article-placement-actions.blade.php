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
