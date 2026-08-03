import { paintValuesStory, clearValuesStory } from './paint.js';
import {
    FRAME_MS,
    createScrollMotion,
    readStoryProgress,
    resetScrollMotion,
    updateScrollMotion,
} from './motion.js';

function supportsStoryMotion() {
    return typeof CSS !== 'undefined'
        && CSS.supports('overflow', 'clip')
        && CSS.supports('position', 'sticky')
        && CSS.supports('transform-style', 'preserve-3d');
}

export function createValuesStory(root) {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const cards = Array.from(root.querySelectorAll('[data-values-card]'));

    if (cards.length !== 4 || reduced.matches || !supportsStoryMotion()) {
        return () => {};
    }

    let frame = 0;
    let observer = null;
    let active = true;
    let destroyed = false;
    let geometryDirty = true;
    let snapNext = true;
    let lastTime = 0;
    let viewportHeight = window.innerHeight || 1;
    let geometry = null;
    const motion = createScrollMotion(0);

    root.classList.add('is-values-ready');

    function cancelFrame() {
        if (frame) window.cancelAnimationFrame(frame);
        frame = 0;
        lastTime = 0;
    }

    function requestRender() {
        if (!frame && active && !destroyed && !document.hidden) {
            frame = window.requestAnimationFrame(render);
        }
    }

    function measure() {
        geometryDirty = false;
        viewportHeight = window.innerHeight || 1;
        const rect = cards[0].getBoundingClientRect();
        const styles = window.getComputedStyle(root);

        geometry = {
            cardWidth: Math.max(1, rect.width),
            cardHeight: Math.max(1, rect.height),
            mode: Number.parseInt(
                styles.getPropertyValue('--values-layout-mode'),
                10,
            ) || 1,
            viewportHeight,
            viewportWidth: window.innerWidth || 1,
        };
    }

    function render(time) {
        frame = 0;
        if (!active || destroyed || document.hidden) return;
        if (geometryDirty || !geometry) measure();

        const target = readStoryProgress(root, viewportHeight);
        const delta = lastTime ? time - lastTime : FRAME_MS;
        lastTime = time;
        const snapshot = updateScrollMotion(
            motion,
            target,
            delta,
            snapNext,
        );

        paintValuesStory(
            root,
            cards,
            snapshot.visual,
            geometry,
            snapshot.momentum,
        );
        snapNext = false;

        if (!snapshot.settled) requestRender();
        else lastTime = 0;
    }

    function onScroll() {
        requestRender();
    }

    function onResize() {
        geometryDirty = true;
        snapNext = true;
        requestRender();
    }

    function onVisibility() {
        if (document.hidden) cancelFrame();
        else onResize();
    }

    function onIntersection(entries) {
        const nextActive = entries.some((entry) => entry.isIntersecting);
        if (nextActive === active) return;

        active = nextActive;
        cancelFrame();
        if (!active) return;

        geometryDirty = true;
        const target = readStoryProgress(root, window.innerHeight || 1);
        resetScrollMotion(motion, target);
        snapNext = true;
        requestRender();
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', onResize);
    document.addEventListener('visibilitychange', onVisibility);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '100% 0px 100% 0px',
            threshold: 0.01,
        });
        observer.observe(root);
    }

    resetScrollMotion(
        motion,
        readStoryProgress(root, viewportHeight),
    );
    requestRender();

    return function destroy() {
        destroyed = true;
        active = false;
        cancelFrame();
        observer?.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', onResize);
        document.removeEventListener('visibilitychange', onVisibility);
        root.classList.remove('is-values-ready');
        clearValuesStory(root, cards);
    };
}

const valuesStory = document.querySelector('[data-values-story]');
if (valuesStory) createValuesStory(valuesStory);
