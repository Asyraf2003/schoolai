import { desktopCardFrame } from './values-card-desktop.js';
import { responsiveFlipAngle } from './values-card-keyframes.js';
import { clamp, createScrollMotion, updateScrollMotion } from './values-card-motion.js';

export function mountValuesCards(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const stage = root.querySelector('[data-values-cards-stage]');
    const viewport = root.querySelector('[data-values-cards-viewport]');
    const track = root.querySelector('[data-values-cards-track]');
    const cards = [...root.querySelectorAll('[data-values-card]')];
    const poses = cards.map(card => card.querySelector('[data-values-card-pose]'));
    const flips = cards.map(card => card.querySelector('[data-values-card-inner]'));
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const spring = createScrollMotion();
    const capable = 'IntersectionObserver' in window && CSS.supports('transform-style', 'preserve-3d')
        && CSS.supports('perspective', '800px') && CSS.supports('overflow', 'clip');
    let near = false;
    let suspended = false;
    let frame = 0;
    let last = 0;
    let geometry;
    let dirty = true;
    let snap = true;
    const running = () => {
        root.toggleAttribute('data-cards-running', near && !suspended && !document.hidden
            && root.hasAttribute('data-cards-motion'));
    };
    const cancel = () => { cancelAnimationFrame(frame); frame = 0; last = 0; running(); };
    const reset = () => {
        delete root.dataset.cardsMotion;
        delete root.dataset.cardsProgress;
        poses.forEach(pose => pose.style.removeProperty('transform'));
        flips.forEach(flip => flip.style.removeProperty('transform'));
        snap = true; running();
    };
    const measure = () => {
        const desktop = innerWidth >= 1280;
        const headline = root.querySelector('.values__header');
        const overlap = Math.min(160, Math.max(112, innerHeight * .15));
        track.style.setProperty('--values-cards-start', `${headline.offsetTop + headline.offsetHeight - overlap}px`);
        const heights = cards.map(card => card.offsetHeight);
        // Read the resolved length: the shared token itself contains min/clamp.
        const header = parseFloat(getComputedStyle(root).scrollMarginBlockStart) || 0;
        const tooTall = Math.max(...heights) + 48 > innerHeight - header;
        const flow = !capable || motion.matches || tooTall;
        root.toggleAttribute('data-cards-flow', flow);
        const stageBox = viewport.getBoundingClientRect();
        const rects = cards.map(card => card.getBoundingClientRect());
        const mode = desktop ? 4 : innerWidth >= 768 ? 3 : 1;
        if (geometry?.mode !== mode) snap = true;
        geometry = {
            mode, flow, stageHeight: stageBox.height,
            stageCenterX: stageBox.left + stageBox.width / 2,
            stageCenterY: stageBox.top + stageBox.height / 2,
            cardWidth: rects[0].width, cardHeight: Math.max(...heights),
            rowGap: parseFloat(getComputedStyle(stage).rowGap) || 0,
            stickyTop: header,
            slots: rects.map(rect => ({ centerX: rect.left + rect.width / 2, centerY: rect.top + rect.height / 2 })),
        };
        dirty = false;
    };
    const paint = time => {
        frame = 0;
        if (suspended || document.hidden || !near) return;
        if (dirty) measure();
        if (!capable || motion.matches || geometry.flow || !root.hasAttribute('data-line-ready')) { reset(); return; }
        root.dataset.cardsMotion = 'true'; running();
        const desktop = geometry.mode === 4;
        const target = clamp((geometry.stickyTop - track.getBoundingClientRect().top)
            / Math.max(1, track.offsetHeight - viewport.offsetHeight));
        const sample = updateScrollMotion(spring, target, last ? time - last : 1000 / 60, snap || !desktop);
        last = time; snap = false;
        root.dataset.cardsProgress = sample.visual.toFixed(5);
        // Read untransformed slots before writing any of their child poses.
        const rects = desktop ? [] : cards.map(card => card.getBoundingClientRect());
        cards.forEach((card, index) => {
            let state;
            if (desktop) state = desktopCardFrame(index, sample.visual, geometry, sample.momentum);
            else {
                const visible = clamp((innerHeight - rects[index].top) / rects[index].height);
                const local = clamp((visible - (geometry.mode === 3 ? .16 : .5)) / (geometry.mode === 3 ? .62 : .5));
                const tilt = Math.min(20, Math.atan2(geometry.rowGap * .95, geometry.cardWidth) * 180 / Math.PI);
                state = { x: 0, y: 0, z: 0, rz: 0, scale: 1,
                    sy: -(local < .5 ? 1 : -1) * tilt * Math.sin(Math.PI * local),
                    ry: -responsiveFlipAngle(local) };
            }
            poses[index].style.transform = `translate3d(${state.x.toFixed(2)}px, ${state.y.toFixed(2)}px, ${state.z.toFixed(2)}px) skewY(${(state.sy || 0).toFixed(2)}deg) rotateZ(${state.rz.toFixed(2)}deg) scale(${state.scale})`;
            flips[index].style.transform = `rotateY(${state.ry.toFixed(2)}deg)`;
        });
        if (desktop && !sample.settled) schedule(); else last = 0;
    };
    const schedule = () => {
        if (!frame && near && !suspended && !document.hidden) frame = requestAnimationFrame(paint);
    };
    const invalidate = () => {
        dirty = true;
        // Offscreen slots still reflow on resize; old desktop offsets must not
        // remain attached to a newly compact grid while its RAF is suspended.
        if (!near) { measure(); reset(); }
        schedule();
    };
    const observer = capable ? new IntersectionObserver(entries => {
        near = entries.some(entry => entry.isIntersecting);
        if (near) { dirty = true; schedule(); } else cancel();
        running();
    }) : null;
    observer?.observe(track);
    const visibility = capable ? new IntersectionObserver(entries => {
        for (const entry of entries) entry.target.toggleAttribute('data-card-visible', entry.isIntersecting);
    }) : null;
    cards.forEach(card => visibility?.observe(card));
    const changes = new MutationObserver(invalidate);
    changes.observe(root, { attributes: true, attributeFilter: ['data-line-ready'] });
    const resize = typeof ResizeObserver === 'function' ? new ResizeObserver(invalidate) : null;
    cards.forEach(card => resize?.observe(card));
    resize?.observe(root.querySelector('.values__header'));
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', invalidate, { signal });
    motion.addEventListener('change', () => { measure(); if (motion.matches) reset(); schedule(); }, { signal });
    document.addEventListener('visibilitychange', () => { if (document.hidden) cancel(); else schedule(); running(); }, { signal });
    measure();
    return {
        suspend() { suspended = true; cancel(); },
        resume() { suspended = false; invalidate(); running(); },
        dispose() {
            suspended = true; cancel(); reset(); changes.disconnect(); resize?.disconnect(); observer?.disconnect(); visibility?.disconnect();
            lifecycle.abort(); delete root.dataset.cardsFlow; track.style.removeProperty('--values-cards-start');
            cards.forEach(card => card.removeAttribute('data-card-visible'));
        },
    };
}
