import { initMegaMenus } from './welcome-hero/mega-menu.js';
import { initOpeningHero } from './welcome-hero/opening.js';
import { armHeroReadySignal } from './welcome-hero/readiness.js';

function initHero(root) {
    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
    if (!slides.length) return;
    armHeroReadySignal(root, slides[0]);

    if (root.getAttribute('data-hero-mode') === 'carousel' && slides.length > 1) {
        import('./welcome-hero/carousel.js').then(function (module) {
            module.initHeroCarousel(root, slides);
        });
        return;
    }

    initOpeningHero(root, slides[0]);
}

function bootHomepageHero() {
    initMegaMenus();

    document.querySelectorAll('[data-hero-slider]').forEach(initHero);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootHomepageHero, { once: true });
} else {
    bootHomepageHero();
}
