    .stats-manager-shell {
      display: grid;
      gap: 22px;
    }

    .stats-manager-create {
      padding: 26px;
      overflow: visible;
    }

    .stats-manager-section-head {
      margin-bottom: 18px;
    }

    .stats-manager-section-head h2 {
      margin: 4px 0 0;
      font-size: 1.45rem;
      letter-spacing: -0.035em;
    }

    .stats-manager-section-head p {
      margin: 8px 0 0;
      color: var(--admin-muted);
      line-height: 1.65;
    }

    .stats-manager-kicker {
      color: var(--admin-orange);
      font-size: 0.76rem;
      font-weight: 950;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }

    .stats-manager-list {
      display: grid;
      gap: 16px;
    }

    .stats-manager-card {
      padding: 22px;
      border: 1px solid var(--admin-line);
      border-radius: 26px;
      background: var(--admin-panel);
      box-shadow: var(--admin-shadow);
    }

    .stats-manager-card__head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 18px;
      margin-bottom: 18px;
    }

    .stats-manager-card__head strong,
    .stats-manager-card__head small {
      display: block;
    }

    .stats-manager-card__head strong {
      margin-top: 7px;
      font-size: 1.15rem;
      line-height: 1.4;
    }

    .stats-manager-card__head small {
      margin-top: 5px;
      color: var(--admin-muted);
      font-weight: 750;
    }

    .stats-manager-form {
      margin: 0;
    }

    .stats-manager-language-grid {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 14px;
    }

    .stats-manager-language {
      padding: 17px;
      border: 1px solid var(--admin-line);
      border-radius: 20px;
      background: #fffdf8;
    }

    .stats-manager-language h3 {
      margin: 0 0 14px;
      font-size: 0.92rem;
      letter-spacing: 0.02em;
    }

    .stats-manager-fields {
      display: grid;
      grid-template-columns: minmax(130px, 0.65fr) minmax(210px, 1.35fr);
      gap: 12px;
    }

    .stats-manager-form-actions {
      display: flex;
      justify-content: flex-end;
      margin-top: 16px;
    }

    .stats-manager-card__actions {
      display: flex;
      justify-content: flex-end;
      margin-top: 16px;
      padding-top: 16px;
      border-top: 1px solid var(--admin-line);
    }

    .stats-manager-card__actions form {
      margin: 0;
    }

    .stats-manager-card__actions button:disabled {
      opacity: 0.42;
      cursor: not-allowed;
      transform: none;
    }
