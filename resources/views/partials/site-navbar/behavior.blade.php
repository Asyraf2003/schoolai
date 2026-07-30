<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}">
  document.addEventListener('DOMContentLoaded', function () {
    var modal = document.querySelector('[data-language-modal]');
    var triggers = Array.prototype.slice.call(document.querySelectorAll('[data-language-modal-open]'));

    if (!modal || !triggers.length) return;

    var dialog = modal.querySelector('.language-modal__dialog');
    var closeControls = Array.prototype.slice.call(modal.querySelectorAll('[data-language-modal-close]'));
    var hamburger = document.getElementById('hamburgerBtn');
    var navLayer = document.getElementById('navMenu');
    var header = document.getElementById('navbar');
    var lastFocused = null;
    var previousOverflow = '';

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
      var openedFromMobileMenu = navLayer && navLayer.classList.contains('active');
      lastFocused = openedFromMobileMenu && hamburger
        ? hamburger
        : (trigger || document.activeElement);

      closeMobileMenu();
      previousOverflow = document.body.style.overflow;
      document.body.style.overflow = 'hidden';
      modal.classList.add('is-open');
      modal.setAttribute('aria-hidden', 'false');

      window.requestAnimationFrame(function () {
        if (dialog) dialog.focus({ preventScroll: true });
      });
    }

    function closeLanguageModal() {
      if (!modal.classList.contains('is-open')) return;

      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = previousOverflow;

      if (lastFocused && document.documentElement.contains(lastFocused)) {
        lastFocused.focus({ preventScroll: true });
      }
    }

    triggers.forEach(function (trigger) {
      trigger.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();
        openLanguageModal(trigger);
      });
    });

    closeControls.forEach(function (control) {
      control.addEventListener('click', closeLanguageModal);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('is-open')) {
        event.preventDefault();
        closeLanguageModal();
      }
    });
  });
</script>
