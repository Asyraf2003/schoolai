@php
  $flagLocale = in_array(($locale ?? null), ['id', 'en', 'ar'], true)
    ? $locale
    : 'id';
@endphp

@once
  <style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    /* Keep the original flag-only chooser, but center it in the viewport modal. */
    .language-modal__dialog {
      width: auto;
      min-height: 0;
      padding: 0;
      overflow: visible;
      border: 0;
      border-radius: 0;
      color: inherit;
      background: transparent;
      box-shadow: none;
    }

    .language-modal__title,
    .language-modal__close,
    .language-modal__label {
      display: none;
    }

    .language-modal__options {
      align-items: center;
      gap: clamp(28px, 6vw, 84px);
    }

    .language-modal__flag {
      width: clamp(96px, 10vw, 142px);
      height: clamp(96px, 10vw, 142px);
    }

    .navbar__menu .nav-language__button {
      position: relative;
      z-index: 2;
      pointer-events: auto !important;
      touch-action: manipulation;
      cursor: pointer;
    }

    @media (max-width: 640px) {
      .language-modal__dialog {
        width: auto;
        min-height: 0;
        padding: 0;
        border-radius: 0;
      }

      .language-modal__options {
        gap: clamp(16px, 5vw, 26px);
      }

      .language-modal__flag {
        width: clamp(76px, 23vw, 104px);
        height: clamp(76px, 23vw, 104px);
      }
    }
  </style>

  <script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
    /*
     * Delegated fallback for the language trigger.
     * This intentionally does not wait for DOMContentLoaded, so the mobile
     * button remains reliable even when the navbar/menu scripts initialize
     * in a different order.
     */
    document.addEventListener('click', function (event) {
      var trigger = event.target.closest('[data-language-modal-open]');
      if (!trigger) return;

      var modal = document.getElementById('languageModal');
      if (!modal) return;

      event.preventDefault();
      event.stopImmediatePropagation();

      var navMenu = document.getElementById('navMenu');
      var navOverlay = document.getElementById('navOverlay');
      var hamburger = document.getElementById('hamburgerBtn');

      if (navMenu) navMenu.classList.remove('active');
      if (navOverlay) navOverlay.classList.remove('active');

      if (hamburger) {
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.setAttribute(
          'aria-label',
          hamburger.getAttribute('data-mobile-open-label') || 'Open menu'
        );
      }

      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';

      var dialog = modal.querySelector('.language-modal__dialog');
      if (dialog) {
        window.requestAnimationFrame(function () {
          dialog.focus({ preventScroll: true });
        });
      }
    }, true);
  </script>
@endonce

<span class="nav-language__flag nav-language__flag--{{ $flagLocale }}" aria-hidden="true">
  @switch($flagLocale)
    @case('en')
      <svg viewBox="0 0 60 60" focusable="false">
        <rect width="60" height="60" fill="#21468b" />
        <path d="M0 0 60 60M60 0 0 60" stroke="#fff" stroke-width="13" />
        <path d="M0 0 60 60M60 0 0 60" stroke="#cf142b" stroke-width="6" />
        <path d="M30 0v60M0 30h60" stroke="#fff" stroke-width="18" />
        <path d="M30 0v60M0 30h60" stroke="#cf142b" stroke-width="10" />
      </svg>
      @break

    @case('ar')
      <svg viewBox="0 0 60 60" focusable="false">
        <rect width="60" height="20" fill="#159447" />
        <rect y="20" width="60" height="20" fill="#fff" />
        <rect y="40" width="60" height="20" fill="#111" />
        <rect width="16" height="60" fill="#d8232a" />
      </svg>
      @break

    @default
      <svg viewBox="0 0 60 60" focusable="false">
        <rect width="60" height="30" fill="#e70011" />
        <rect y="30" width="60" height="30" fill="#fff" />
      </svg>
  @endswitch
</span>
