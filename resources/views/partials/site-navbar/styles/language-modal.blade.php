  .nav-language__flag {
    width: 100%;
    height: 100%;
    display: block;
    overflow: hidden;
    border-radius: inherit;
  }

  .nav-language__flag svg {
    width: 100%;
    height: 100%;
    display: block;
  }

  .navbar--public .nav-language__current-flag {
    border-color: rgba(31, 46, 43, 0.12);
  }

  .language-modal {
    position: fixed;
    inset: 0;
    z-index: 120;
    display: grid;
    place-items: center;
    padding: clamp(20px, 4vw, 48px);
    opacity: 0;
    visibility: hidden;
    pointer-events: none;
    transition:
      opacity 220ms ease,
      visibility 0s linear 260ms;
  }

  .language-modal.is-open {
    opacity: 1;
    visibility: visible;
    pointer-events: auto;
    transition-delay: 0s;
  }

  .language-modal__backdrop {
    position: absolute;
    inset: 0;
    border: 0;
    background: rgba(2, 16, 14, 0.76);
    -webkit-backdrop-filter: blur(14px) saturate(115%);
    backdrop-filter: blur(14px) saturate(115%);
  }

  .language-modal__dialog {
    position: relative;
    z-index: 1;
    width: min(760px, calc(100vw - 40px));
    min-height: 340px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: clamp(38px, 6vw, 72px) clamp(24px, 6vw, 64px);
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.72);
    border-radius: 32px;
    color: #17362f;
    background:
      radial-gradient(circle at 50% 0%, rgba(247, 178, 75, 0.18), transparent 42%),
      rgba(255, 250, 241, 0.98);
    box-shadow: 0 36px 110px rgba(0, 0, 0, 0.38);
    opacity: 0;
    transform: translateY(18px) scale(0.94);
    transition:
      opacity 220ms ease,
      transform 360ms cubic-bezier(0.22, 1, 0.36, 1);
  }

  .language-modal.is-open .language-modal__dialog {
    opacity: 1;
    transform: translateY(0) scale(1);
  }

  .language-modal__close {
    position: absolute;
    inset-block-start: 18px;
    inset-inline-end: 18px;
    width: 44px;
    height: 44px;
    display: grid;
    place-items: center;
    border: 1px solid rgba(23, 54, 47, 0.14);
    border-radius: 50%;
    color: #17362f;
    background: rgba(255, 255, 255, 0.72);
    font-size: 1.65rem;
    line-height: 1;
    cursor: pointer;
  }

  .language-modal__title {
    margin: 0 0 clamp(28px, 5vw, 48px);
    color: #17362f;
    font-family: var(--font-display);
    font-size: clamp(1.5rem, 3vw, 2.25rem);
    font-weight: 820;
    text-align: center;
  }

  .language-modal__options {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    gap: clamp(22px, 5vw, 54px);
  }

  .language-modal__form {
    margin: 0;
  }

  .language-modal__option {
    display: grid;
    justify-items: center;
    gap: 14px;
    border: 0;
    color: #294c44;
    background: transparent;
    font: inherit;
    cursor: pointer;
  }

  .language-modal__flag {
    width: clamp(96px, 10vw, 142px);
    height: clamp(96px, 10vw, 142px);
    display: block;
    overflow: hidden;
    border: 5px solid rgba(255, 255, 255, 0.96);
    border-radius: 50%;
    background: #fff;
    box-shadow:
      0 16px 38px rgba(20, 45, 39, 0.2),
      0 0 0 1px rgba(23, 54, 47, 0.08);
    transition:
      transform 180ms ease,
      border-color 180ms ease,
      box-shadow 180ms ease;
  }

  .language-modal__option:hover .language-modal__flag,
  .language-modal__option:focus-visible .language-modal__flag {
    border-color: #f7c66f;
    box-shadow:
      0 0 0 6px rgba(247, 198, 111, 0.18),
      0 22px 46px rgba(20, 45, 39, 0.24);
    transform: translateY(-5px) scale(1.03);
  }

  .language-modal__option.is-active .language-modal__flag {
    border-color: #f7b24b;
    box-shadow:
      0 0 0 7px rgba(247, 178, 75, 0.2),
      0 20px 42px rgba(20, 45, 39, 0.22);
  }

  .language-modal__label {
    font-size: 0.82rem;
    font-weight: 800;
  }
