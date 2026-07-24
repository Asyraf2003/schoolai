  @media (max-width: 1180px) {
    .navbar__menu .nav-language {
      width: 100%;
      margin-block-start: 10px;
      padding-block-start: 16px;
      border-block-start: 1px solid rgba(51, 49, 77, 0.1);
    }

    .navbar__menu .nav-language__button {
      display: flex !important;
      width: 100%;
      justify-content: flex-start !important;
      color: #17362f;
    }

    .navbar__menu .nav-language__current-flag {
      display: none;
    }
  }

  @media (max-width: 640px) {
    .language-modal {
      padding: 16px;
    }

    .language-modal__dialog {
      width: min(100%, 430px);
      min-height: 300px;
      padding: 58px 18px 38px;
      border-radius: 26px;
    }

    .language-modal__title {
      margin-bottom: 30px;
      font-size: 1.45rem;
    }

    .language-modal__options {
      gap: clamp(12px, 4vw, 22px);
    }

    .language-modal__flag {
      width: clamp(76px, 23vw, 104px);
      height: clamp(76px, 23vw, 104px);
      border-width: 4px;
    }

    .language-modal__label {
      font-size: 0.72rem;
    }
  }

  @media (prefers-reduced-motion: reduce) {
    .language-modal,
    .language-modal__dialog,
    .language-modal__flag {
      transition-duration: 0.01ms !important;
    }
  }
