const clamp = value => Math.min(1, Math.max(0, value));
const ease = value => { const p = clamp(value); return p * p * (3 - 2 * p); };

// Scroll-driven, reversible CSS3D. No GSAP instance, timer or added runway.
export function mountValuesCards(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const stage = root.querySelector('[data-values-cards-stage]');
    const cards = [...root.querySelectorAll('[data-values-card]')];
    if (!stage || cards.length !== 4) return { suspend() {}, resume() {}, dispose() {} };
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let observer;
    let near = false;
    let suspended = false;
    let frame = 0;
    let active = false;
    const cancel = () => { if (frame) cancelAnimationFrame(frame); frame = 0; };
    const reset = () => {
        if (!active) return;
        active = false;
        delete root.dataset.cardsMotion;
        for (const card of cards) {
            card.querySelector('[data-values-card-pose]').style.removeProperty('transform');
            card.querySelector('[data-values-card-inner]').style.removeProperty('transform');
        }
    };
    const paint = () => {
        frame = 0;
        if (suspended || document.hidden || !near) return;
        if (motion.matches || !root.hasAttribute('data-line-ready')) { reset(); return; }
        active = true;
        root.dataset.cardsMotion = 'true';
        const screen = document.documentElement.clientHeight;
        const desktop = innerWidth >= 1280;
        if (desktop) {
            const progress = clamp((-root.getBoundingClientRect().top - screen * .6) / (screen * 3.6));
            const spread = ease(progress / .22);
            const straighten = ease((progress - .63) / .14);
            const exit = ease((progress - .84) / .16);
            const gap = parseFloat(getComputedStyle(stage).columnGap) || 0;
            for (const [index, card] of cards.entries()) {
                const distance = (1.5 - index) * (card.offsetWidth + gap) * (1 - spread);
                const fan = [-13, -4.5, 4.5, 13][index] * spread * (1 - straighten);
                const flip = ease((progress - .18 - index * .045) / .29);
                card.querySelector('[data-values-card-pose]').style.transform =
                    `translate3d(${distance.toFixed(2)}px, ${(-exit * screen * .95).toFixed(2)}px, 0) rotate(${fan.toFixed(2)}deg)`;
                card.querySelector('[data-values-card-inner]').style.transform =
                    `rotateY(${(180 * (1 - flip)).toFixed(2)}deg)`;
            }
        } else {
            for (const [index, card] of cards.entries()) {
                const top = card.getBoundingClientRect().top;
                const flip = ease((screen * .86 - top) / (screen * .65));
                const angle = (index % 2 ? 5 : -5) * (1 - flip);
                card.querySelector('[data-values-card-pose]').style.transform =
                    `translate3d(0, ${((1 - flip) * 20).toFixed(2)}px, 0) rotate(${angle.toFixed(2)}deg)`;
                card.querySelector('[data-values-card-inner]').style.transform =
                    `rotateY(${(180 * (1 - flip)).toFixed(2)}deg)`;
            }
        }
    };
    const schedule = () => {
        if (!frame && !suspended && near && !document.hidden) frame = requestAnimationFrame(paint);
    };
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(entries => {
            near = entries.some(entry => entry.isIntersecting);
            if (near) schedule(); else { cancel(); reset(); }
        }, { rootMargin: '30% 0px' });
        observer.observe(root);
    }
    const changes = new MutationObserver(() => { if (near) schedule(); else reset(); });
    changes.observe(root, { attributes: true, attributeFilter: ['data-line-ready'] });
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', schedule, { signal });
    motion.addEventListener('change', () => { if (motion.matches) reset(); else schedule(); }, { signal });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) cancel(); else schedule();
    }, { signal });
    if (!observer) reset();
    return {
        suspend() { suspended = true; cancel(); },
        resume() { suspended = false; schedule(); },
        dispose() { cancel(); reset(); changes.disconnect(); observer?.disconnect(); lifecycle.abort(); },
    };
}
