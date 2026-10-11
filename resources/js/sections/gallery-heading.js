import { mountSectionHeading } from './section-heading.js';

export function mountGalleryHeading(root) {
    const heading = root.querySelector('[data-gallery-heading]');
    const values = root.previousElementSibling?.querySelector('[data-values]');
    const cards = [...(values?.querySelectorAll('[data-values-card-inner]') ?? [])];
    const viewport = values?.querySelector('[data-values-cards-viewport]');
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let controller;
    let suspended = false;
    let near = false;
    const start = () => {
        controller ??= mountSectionHeading(root, 'gallery', 900, 108);
    };
    const synchronize = () => {
        if (suspended || document.hidden) return;
        if (motion.matches || !cards.length || !('IntersectionObserver' in window) || !heading.animate) {
            root.removeAttribute('data-gallery-heading-waiting');
            start();
            return;
        }
        if (!near) return;
        const clipTop = getComputedStyle(viewport).position === 'sticky'
            ? Math.max(0, viewport.getBoundingClientRect().top) : 0;
        const remaining = Math.max(...cards.map(card => {
            const box = card.getBoundingClientRect();
            return Math.max(0, (box.bottom - clipTop) / Math.max(1, box.height));
        }));
        root.dataset.galleryHeadingRemaining = remaining.toFixed(4);
        root.toggleAttribute('data-gallery-heading-waiting', remaining > .18);
        if (remaining <= .18) start();
    };
    const measure = () => {
        const box = heading.getBoundingClientRect();
        near = box.top < innerHeight * .88 && box.bottom > 0;
        synchronize();
    };
    root.setAttribute('data-gallery-heading-waiting', '');
    const changes = new MutationObserver(synchronize);
    if (values) changes.observe(values, { attributes: true, attributeFilter: ['data-cards-progress', 'data-cards-motion', 'data-cards-flow'] });
    window.addEventListener('scroll', measure, { passive: true, signal });
    window.addEventListener('resize', measure, { signal });
    document.addEventListener('visibilitychange', measure, { signal });
    motion.addEventListener('change', measure, { signal });
    measure();
    return {
        suspend() { suspended = true; controller?.suspend(); },
        resume() { suspended = false; measure(); controller?.resume(); },
        dispose() {
            suspended = true; changes.disconnect(); lifecycle.abort(); controller?.dispose();
            root.removeAttribute('data-gallery-heading-waiting');
            delete root.dataset.galleryHeadingRemaining;
        },
    };
}
