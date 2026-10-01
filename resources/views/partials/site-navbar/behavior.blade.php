<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  (function () {
    var modal = document.querySelector('[data-language-modal]');
    if (!modal || modal.dataset.languageModalBooted === 'true') return;
    modal.dataset.languageModalBooted = 'true';
    var lifecycle = new AbortController();
    var options = { signal: lifecycle.signal };
    var focusFrame = 0;

    var dialog = modal.querySelector('.language-modal__dialog');
    var closeControls = Array.prototype.slice.call(modal.querySelectorAll('[data-language-modal-close]'));
    var hamburger = document.getElementById('hamburgerBtn');
    var navLayer = document.getElementById('navMenu');
    var header = document.getElementById('navbar');
    var lastFocused = null;
    var previousOverflow = '';

    function cancelFocus() {
      if (focusFrame) window.cancelAnimationFrame(focusFrame);
      focusFrame = 0;
    }

    function focusDialog() {
      cancelFocus();
      focusFrame = window.requestAnimationFrame(function () {
        focusFrame = 0;
        if (dialog && modal.classList.contains('is-open')) dialog.focus({ preventScroll: true });
      });
    }

    function closeMobileMenu() {
      if (!navLayer || !navLayer.classList.contains('active')) return;

      document.dispatchEvent(new CustomEvent('mobile-navigation:request-close', {
        detail: { immediate: true }
      }));

      if (!navLayer.classList.contains('active')) return;

      navLayer.classList.remove('active', 'is-closing');
      navLayer.setAttribute('aria-hidden', 'true');
      navLayer.inert = true;
      navLayer.hidden = true;
      document.body.style.overflow = '';
      if (header) header.classList.remove('has-open-menu');

      if (hamburger) {
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.setAttribute(
          'aria-label',
          hamburger.getAttribute('data-mobile-open-label') || 'Open menu'
        );
      }
    }

    function openLanguageModal(trigger) {
      if (modal.classList.contains('is-open')) return;
      var openedFromMobileMenu = navLayer && navLayer.classList.contains('active');
      lastFocused = openedFromMobileMenu && hamburger
        ? hamburger
        : (trigger || document.activeElement);

      closeMobileMenu();
      previousOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');

      focusDialog();
    }

    function closeLanguageModal() {
      if (!modal.classList.contains('is-open')) return;
      cancelFocus();
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = previousOverflow;

      if (lastFocused && document.documentElement.contains(lastFocused)) {
        lastFocused.focus({ preventScroll: true });
      }
    }

    document.addEventListener('click', function (event) {
      var trigger = event.target.closest('[data-language-modal-open]');
      if (!trigger) return;
      event.preventDefault();
      event.stopPropagation();
      openLanguageModal(trigger);
    }, { capture: true, signal: lifecycle.signal });

    closeControls.forEach(function (control) {
      control.addEventListener('click', closeLanguageModal, options);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('is-open')) {
        event.preventDefault();
        closeLanguageModal();
      }
      if (event.key !== 'Tab' || !modal.classList.contains('is-open') || !dialog) return;
      var controls = Array.prototype.slice.call(dialog.querySelectorAll(
        'button:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])'
      )).filter(function (control) { return control.getClientRects().length; });
      if (!controls.length) { event.preventDefault(); return; }
      var first = controls[0];
      var last = controls[controls.length - 1];
      if (!dialog.contains(document.activeElement) || document.activeElement === dialog
          || (event.shiftKey && document.activeElement === first)
          || (!event.shiftKey && document.activeElement === last)) {
        event.preventDefault();
        (event.shiftKey ? last : first).focus();
      }
    }, options);
    window.addEventListener('pagehide', function (event) {
      cancelFocus();
      if (event.persisted) return;
      closeLanguageModal();
      lifecycle.abort();
    }, options);
    window.addEventListener('pageshow', function () {
      if (dialog && modal.classList.contains('is-open') && !dialog.contains(document.activeElement)) focusDialog();
    }, options);
  })();
</script>
