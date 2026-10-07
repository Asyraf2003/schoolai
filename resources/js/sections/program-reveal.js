export function observeProgram(root, signal) {
    const heading = root.querySelector('[data-program-heading]');
    const cards = [...root.querySelectorAll('[data-program-card]')];
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let observer;
    let proximity;
    let background;
    let visible = false;
    const paintBackground = () => {
        root.dataset.programBackgroundActive = String(visible && !document.hidden && !motion.matches);
    };
    let refresh = () => {};
    const clear = () => {
        observer?.disconnect();
        proximity?.disconnect();
        background?.disconnect();
        visible = false;
        delete root.dataset.programBackgroundActive;
        delete root.dataset.revealEnabled;
        refresh = () => {};
    };
    const configure = () => {
        clear();
        if (!('IntersectionObserver' in window)) return;
        proximity = new IntersectionObserver(entries => {
            if (!entries.some(entry => entry.isIntersecting)) return;
            root.style.setProperty('--program-entry-texture', `url("${root.dataset.entryTexture}")`);
            proximity.disconnect();
        }, { rootMargin: '25% 0px' });
        proximity.observe(root);
        if (motion.matches) return;
        background = new IntersectionObserver(entries => {
            visible = entries.some(entry => entry.isIntersecting);
            paintBackground();
        });
        background.observe(root.closest('[data-program-values]') ?? root);
        const reached = new Set();
        const paintCards = () => {
            const frontier = Math.max(-1, ...reached);
            cards.forEach((card, index) => { card.dataset.revealed = String(index <= frontier); });
        };
        refresh = () => {
            if (root.dataset.programState && root.dataset.programState !== 'idle') return;
            const boundary = document.documentElement.clientHeight - document.documentElement.clientWidth * .12;
            reached.clear();
            cards.forEach((card, index) => {
                const box = card.getBoundingClientRect();
                if (box.top < 0 || Math.max(0, Math.min(box.bottom, boundary) - Math.max(box.top, 0)) >= box.height * .08) reached.add(index);
            });
            const box = heading.getBoundingClientRect();
            heading.dataset.revealed = String(Math.max(0, Math.min(box.bottom, boundary) - Math.max(box.top, 0)) >= box.height * .08);
            paintCards();
        };
        observer = new IntersectionObserver(entries => {
            if (root.dataset.programState && root.dataset.programState !== 'idle') return;
            for (const entry of entries) {
                const inView = entry.isIntersecting && entry.intersectionRatio >= .08;
                const passed = entry.boundingClientRect.top < entry.rootBounds.top;
                if (entry.target === heading) heading.dataset.revealed = String(inView);
                else {
                    const index = cards.indexOf(entry.target);
                    if (inView || passed) reached.add(index);
                    else reached.delete(index);
                }
            }
            paintCards();
        }, { rootMargin: '0px 0px -12% 0px', threshold: [0, .08] });
        [heading, ...cards].forEach(element => observer.observe(element));
        cards.forEach((card, index) => card.style.setProperty('--program-reveal-delay', `${index % 4 * 60}ms`));
        root.dataset.revealEnabled = 'true';
    };
    motion.addEventListener('change', configure, { signal });
    document.addEventListener('visibilitychange', paintBackground, { signal });
    // IO cannot report an above→below jump when both positions are non-intersecting.
    // Reconcile visibility once after native scroll ends; never calculate layout.
    document.addEventListener('scrollend', event => { if (event.target === document) refresh(); }, { signal });
    window.addEventListener('resize', () => refresh(), { signal });
    root.addEventListener('program:restored', () => refresh(), { signal });
    configure();
    return clear;
}
