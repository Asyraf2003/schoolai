@media (min-width: 1181px) {
  .nav-shell .navbar__menu > ul > .nav-item > .nav-link,
  .nav-shell .navbar__menu > .navbar__cta {
    font-size: clamp(1rem, 0.84rem + 0.2vw, 1.1rem);
  }

  .nav-shell .nav-mega__panel {
    grid-template-columns: minmax(380px, 560px) minmax(300px, 1fr);
    gap: clamp(24px, 3vw, 48px);
  }

  .nav-shell .nav-login .nav-mega__panel {
    inset-inline-start: auto;
    inset-inline-end: 0;
    width: min(420px, calc(100vw - 48px));
    grid-template-columns: 1fr;
  }

  .nav-shell .nav-login .nav-mega__media {
    display: none;
  }

  .nav-shell .nav-mega__links {
    grid-template-columns: minmax(0, 1fr);
    align-content: center;
    gap: 0;
    width: 100%;
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
