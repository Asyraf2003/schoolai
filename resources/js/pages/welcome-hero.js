import { initializeHomeHero } from '../surfaces/home/hero/controller.js';

function bootHomeHero() {
  document.querySelectorAll('[data-hero-slider]').forEach(function (root) {
    initializeHomeHero(root);
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', bootHomeHero, { once: true });
} else {
  bootHomeHero();
}
