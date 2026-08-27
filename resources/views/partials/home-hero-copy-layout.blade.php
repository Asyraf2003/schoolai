@once
  @vite([
    'resources/css/pages/welcome-mega-menu.css',
    'resources/css/pages/welcome-hero-motion.css',
    'resources/css/pages/welcome-hero-visual.css',
  ])

  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    .home-page .hero-cinema__content {
      padding-block-end: clamp(108px, 12vh, 142px);
    }

    .home-page .hero-cinema__copy {
      width: min(620px, 41vw);
      max-width: 620px;
    }

    .home-page .hero-cinema__title {
      max-width: 22ch;
      margin: 0;
      text-wrap: balance;
      text-shadow:
        0 2px 3px rgb(0 0 0 / 0.96),
        0 0 12px rgb(0 0 0 / 0.86),
        0 0 30px rgb(0 0 0 / 0.68),
        0 0 56px rgb(0 0 0 / 0.48);
    }

    @media (max-width: 1180px) {
      .home-page .hero-cinema__content {
        padding-block-end: clamp(104px, 12vh, 132px);
      }

      .home-page .hero-cinema__copy {
        width: min(560px, 58vw);
        max-width: 560px;
      }

      .home-page .hero-cinema__title {
        max-width: 20ch;
      }
    }

    @media (max-width: 767px) {
      .home-page .hero-cinema__content {
        align-items: flex-end;
        padding-block-start: calc(var(--hero-nav-height) + 28px);
        padding-block-end: 108px;
      }

      .home-page .hero-cinema__copy {
        width: min(84vw, 340px);
        max-width: min(84vw, 340px);
      }

      .home-page .hero-cinema__title {
        max-width: 18ch;
      }
    }

    @media (max-width: 440px) {
      .home-page .hero-cinema__content {
        padding-block-end: 102px;
      }

      .home-page .hero-cinema__copy {
        width: min(82vw, 315px);
        max-width: min(82vw, 315px);
      }

      .home-page .hero-cinema__title {
        max-width: 17ch;
      }
    }
  </style>
@endonce
