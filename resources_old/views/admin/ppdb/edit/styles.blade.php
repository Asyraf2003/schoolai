  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    .ppdb-showcase-admin { margin-top: 22px; display: grid; gap: 18px; }
    .ppdb-showcase-admin__grid { display: grid; grid-template-columns: minmax(0, 1fr) minmax(360px, 0.68fr); gap: 18px; align-items: start; }
    .ppdb-showcase-admin__panel { padding: 18px; }
    .ppdb-showcase-admin__head { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; margin-bottom: 14px; }
    .ppdb-showcase-admin__head h2, .ppdb-showcase-admin__head p { margin: 0; }
    .ppdb-showcase-admin__head p { margin-top: 6px; color: var(--admin-muted); line-height: 1.55; }
    .ppdb-showcase-list { display: grid; gap: 14px; }
    .ppdb-showcase-group { border: 1px solid var(--admin-line); border-radius: 18px; overflow: hidden; background: #fff; }
    .ppdb-showcase-group__title { display: flex; justify-content: space-between; gap: 12px; padding: 12px 14px; background: #fffdf8; border-bottom: 1px solid var(--admin-line); font-weight: 950; }
    .ppdb-showcase-row { display: grid; grid-template-columns: 42px minmax(0, 1fr) 104px minmax(260px, auto); align-items: center; gap: 12px; padding: 13px 14px; border-bottom: 1px solid var(--admin-line); }
    .ppdb-showcase-row:last-child { border-bottom: 0; }
    .ppdb-showcase-row__order { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 999px; background: #f6f3ec; color: var(--admin-ink); font-weight: 950; }
    .ppdb-showcase-row__body { min-width: 0; display: grid; gap: 3px; }
    .ppdb-showcase-row__body strong, .ppdb-showcase-row__body small { overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
    .ppdb-showcase-row__body small { color: var(--admin-muted); }
    .ppdb-showcase-media-pill { justify-self: end; padding: 6px 10px; border-radius: 999px; background: #eef2ff; color: #3730a3; font-size: 0.78rem; font-weight: 950; }
    .ppdb-showcase-form-note { margin: 0 0 14px; padding: 12px 14px; border-radius: 16px; background: #fff7ed; color: #9a3412; font-weight: 780; line-height: 1.55; }
    .ppdb-showcase-preview { margin-top: 14px; }
    .ppdb-showcase-preview__stage { min-height: 190px; }
    .ppdb-showcase-preview__stage img, .ppdb-showcase-preview__stage iframe { width: 100%; min-height: 190px; display: block; border: 0; object-fit: cover; background: #111827; }
    .ppdb-media-input[hidden] { display: none; }
    @media (max-width: 1180px) { .ppdb-showcase-admin__grid { grid-template-columns: 1fr; } }
  </style>
