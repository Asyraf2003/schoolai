@media (min-width: 1181px) {
  .nav-shell .navbar__menu > ul > .nav-item > .nav-link,
  .nav-shell .navbar__menu > .navbar__cta {
    font-size: clamp(1rem, 0.84rem + 0.2vw, 1.1rem);
  }

  /*
   * Desktop mega geometry is a navbar contract, not a stylesheet-order side
   * effect. These values mirror the approved full-width mega surface so the
   * header cannot fall back to the older inset/rounded hero panel while CSS
   * delivery is tuned for performance.
   */
  .nav-shell .navbar.has-open-menu {
    height: 78px;
    background: var(--nav-mega-surface, #fffaf1) !important;
    -webkit-backdrop-filter: none;
    backdrop-filter: none;
    box-shadow: none !important;
    border-block-end: 0 !important;
  }

  .nav-shell .nav-mega__panel {
    position: fixed;
    inset-block-start: 77px;
    inset-inline: 0;
    width: 100vw;
    min-height: clamp(400px, 50vh, 540px);
    max-height: calc(100dvh - 78px);
    grid-template-columns: minmax(360px, 560px) minmax(0, 1fr);
    grid-template-rows: auto;
    gap: clamp(38px, 5vw, 82px);
    align-items: center;
    padding: clamp(30px, 4vh, 48px) var(--hero-content-gutter) clamp(36px, 5vh, 58px);
    overflow-y: auto;
    overflow-x: hidden;
    border: 0;
    border-radius: 0;
    background: var(--nav-mega-surface, #fffaf1);
    box-shadow: none;
    transform: translateY(-10px);
    transform-origin: top center;
    clip-path: none;
  }

  .nav-shell .nav-mega.is-open .nav-mega__panel {
    transform: translateY(0);
    clip-path: none;
  }

  .nav-shell .nav-mega__media {
    position: relative;
    width: 100%;
    height: clamp(280px, 34vh, 360px);
    min-height: 0;
    align-self: center;
    overflow: hidden;
    border-radius: 18px;
    background: #103c33;
    box-shadow: 0 20px 48px rgb(3 18 16 / 0.18);
  }

  .nav-shell .nav-mega__links {
    grid-template-columns: repeat(2, minmax(0, 1fr));
    align-content: center;
    gap: 0 clamp(34px, 4vw, 72px);
    width: 100%;
    max-width: none;
    padding: 0;
    background: transparent;
  }

  .nav-shell .nav-login .nav-mega__panel {
    inset-inline-start: auto;
    inset-inline-end: 0;
    width: min(420px, calc(100vw - 48px));
    min-height: 0;
    grid-template-columns: 1fr;
    padding: 18px 24px 24px;
  }

  .nav-shell .nav-login .nav-mega__media {
    display: none;
  }

  .nav-shell .nav-login .nav-mega__links {
    grid-template-columns: 1fr;
    gap: 0;
    max-width: 620px;
  }

  .nav-shell .nav-mega__link {
    min-height: clamp(74px, 8.4vh, 92px);
    gap: clamp(6px, 0.7vh, 9px);
    padding: clamp(14px, 1.8vh, 20px) 0;
  }

  .nav-shell .nav-mega__link strong,
  .nav-shell .nav-mega__link [data-nav-roll="sub"] {
    font-size: clamp(1rem, 0.84rem + 0.2vw, 1.1rem);
    line-height: 1.15;
  }

  .nav-shell .nav-mega__link small {
    max-width: 52ch;
    font-size: clamp(0.86rem, 0.78rem + 0.12vw, 0.96rem);
    line-height: 1.45;
  }
}
