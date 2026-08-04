<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  html,
  body {
    width: 100%;
    max-width: 100%;
    overscroll-behavior-x: none;
  }

  html {
    overflow-x: hidden;
  }

  @supports (overflow: clip) {
    html,
    body {
      overflow-x: clip;
    }
  }

  .navbar.is-scrolled {
    -webkit-backdrop-filter: blur(10px);
    backdrop-filter: blur(10px);
  }

  html[dir="rtl"] body {
    direction: rtl;
    text-align: start;
  }

  html[dir="ltr"] body {
    direction: ltr;
    text-align: start;
  }

  html[dir="rtl"] .nav-link::after {
    right: 0;
    left: auto;
  }

  html[dir="rtl"] .nilai-card,
  html[dir="rtl"] .site-footer__grid {
    text-align: right;
  }

  html[dir="rtl"] .skip-link {
    right: -999px;
    left: auto;
    border-radius: 0 0 0 10px;
  }

  html[dir="rtl"] .skip-link:focus {
    right: 0;
    left: auto;
  }

  @media (max-width: 720px) {
    html[dir="ltr"] .navbar__menu {
      right: 0;
      left: auto;
      transform: translateX(105%);
    }

    html[dir="rtl"] .navbar__menu {
      right: auto;
      left: 0;
      align-items: stretch;
      transform: translateX(-105%);
      box-shadow: 12px 0 30px rgba(0, 0, 0, 0.12);
    }

    html[dir] .navbar__menu.active {
      transform: translateX(0);
    }

    html[dir] .navbar__menu ul {
      width: 100%;
      align-items: stretch;
    }

    html[dir="rtl"] .navbar__menu ul {
      text-align: right;
    }

    html[dir="ltr"] .navbar__menu ul {
      text-align: left;
    }

    html[dir] .navbar__cta {
      width: 100%;
    }
  }
</style>
