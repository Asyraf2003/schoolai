@media (min-width: 1181px) {
  .nav-shell .navbar__menu > ul > .nav-item > .nav-link,
  .nav-shell .navbar__menu > .navbar__cta {
    font-size: clamp(1rem, 0.84rem + 0.2vw, 1.1rem);
  }

  /*
   * Login stays compact. The full desktop mega geometry belongs exclusively
   * to welcome-mega-menu.css so hero/performance delivery cannot redefine it.
   */
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
}
