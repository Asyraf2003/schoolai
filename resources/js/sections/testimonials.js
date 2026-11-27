import { mountTestimonialsWall } from './testimonials-wall.js';

export function mountTestimonials(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };

    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const wall = mountTestimonialsWall(root, signal);
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let touchCard;
    let activeCard;
    let timer;

    function clear() {
        clearTimeout(timer);
        activeCard?.removeAttribute('data-testimonial-lit');
        activeCard = null;
        touchCard = null;
    }

    root.addEventListener('pointerdown', event => {
        touchCard = event.pointerType === 'touch' ? event.target.closest('[data-testimonial-card]') : null;
    }, { signal });
    root.addEventListener('pointercancel', () => { touchCard = null; }, { signal });

    root.addEventListener('click', event => {
        const card = event.target.closest('[data-testimonial-card]');
        const link = event.target.closest('[data-testimonial-link]');
        // WebKit can label the click following a touch pointer as mouse.
        const touch = event.pointerType === 'touch' || touchCard === card;
        touchCard = null;

        if (!card || !touch || !event.detail || event.defaultPrevented || event.button !== 0
            || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey || motion.matches) {
            if (card) clear();
            return;
        }

        if (link) event.preventDefault();
        if (activeCard === card) return;
        clear();
        activeCard = card;
        card.setAttribute('data-testimonial-lit', '');
        timer = setTimeout(() => {
            clear();
            if (link?.isConnected) window.location.assign(link.href);
        }, 700);
    }, { signal });

    document.addEventListener('visibilitychange', () => { if (document.hidden) clear(); }, { signal });
    motion.addEventListener('change', clear, { signal });

    return {
        suspend() { clear(); wall.suspend(); },
        resume: wall.resume,
        dispose() { clear(); lifecycle.abort(); },
    };
}
