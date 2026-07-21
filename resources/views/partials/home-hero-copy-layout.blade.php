@once
  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    /* Keep the hero copy inside the compact lower-corner composition. */
    .home-page .hero-cinema__content {
      padding-block-end: clamp(108px, 12vh, 142px);
    }

    .home-page .hero-cinema__copy {
      width: min(620px, 41vw);
      max-width: 620px;
    }

    .home-page .hero-cinema__eyebrow {
      font-size: clamp(0.62rem, 0.72vw, 0.74rem);
      letter-spacing: 0.17em;
    }

    .home-page .hero-cinema__eyebrow::before {
      width: 32px;
    }

    .home-page .hero-cinema__title {
      max-width: 22ch;
      margin-block-start: clamp(10px, 1.4vh, 16px);
      font-size: clamp(2.2rem, 3vw, 3.9rem);
      line-height: 0.96;
      letter-spacing: -0.045em;
      text-wrap: balance;
    }

    .home-page .hero-cinema__description {
      max-width: 48ch;
      margin-block-start: clamp(14px, 1.8vh, 20px);
      font-size: clamp(0.84rem, 0.92vw, 0.98rem);
      line-height: 1.5;
    }

    .home-page .hero-cinema__cta {
      min-height: 44px;
      margin-block-start: clamp(14px, 2vh, 22px);
      padding-block: 10px;
      font-size: 0.8rem;
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
        font-size: clamp(2.15rem, 4.7vw, 3.35rem);
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

      .home-page .hero-cinema__eyebrow {
        font-size: 0.56rem;
        letter-spacing: 0.13em;
      }

      .home-page .hero-cinema__eyebrow::before {
        width: 22px;
      }

      .home-page .hero-cinema__title {
        max-width: 18ch;
        margin-block-start: 10px;
        font-size: clamp(1.9rem, 8.4vw, 2.55rem);
        line-height: 0.98;
        letter-spacing: -0.04em;
      }

      .home-page .hero-cinema__description {
        max-width: 34ch;
        margin-block-start: 12px;
        font-size: clamp(0.74rem, 3vw, 0.84rem);
        line-height: 1.45;
      }

      .home-page .hero-cinema__cta {
        min-height: 38px;
        margin-block-start: 12px;
        padding-block: 8px;
        font-size: 0.72rem;
      }

      .home-page .hero-cinema__cta svg {
        width: 15px;
        height: 15px;
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
        font-size: clamp(1.72rem, 7.8vw, 2.18rem);
      }

      .home-page .hero-cinema__description {
        max-width: 32ch;
        font-size: 0.74rem;
        -webkit-line-clamp: 3;
      }
    }
  </style>
@endonce
