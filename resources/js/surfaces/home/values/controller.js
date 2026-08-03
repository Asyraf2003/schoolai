import { cardFrame, storyFrame } from './layout.js';
import {
    FRAME_MS,
    createScrollMotion,
    readStoryProgress,
    resetScrollMotion,
    updateScrollMotion,
} from './motion.js';

const MOTION_PROPERTIES = [
    '--values-x', '--values-y', '--values-z', '--values-rx',
    '--values-ry', '--values-rz', '--values-scale', '--values-opacity',
];
function supportsStoryMotion() {
    return typeof CSS !== 'undefined'
        && CSS.supports('overflow', 'clip')
        && CSS.supports('position', 'sticky')
        && CSS.supports('transform-style', 'preserve-3d');
}

function writeCardFrame(card, state) {
    card.style.setProperty('--values-x', `${state.x.toFixed(2)}px`);
    card.style.setProperty('--values-y', `${state.y.toFixed(2)}px`);
    card.style.setProperty('--values-z', `${state.z.toFixed(2)}px`);
    card.style.setProperty('--values-rx', `${state.rx.toFixed(2)}deg`);
    card.style.setProperty('--values-ry', `${state.ry.toFixed(2)}deg`);
    card.style.setProperty('--values-rz', `${state.rz.toFixed(2)}deg`);
    card.style.setProperty('--values-scale', state.scale.toFixed(4));
    card.style.setProperty('--values-opacity', state.opacity.toFixed(4));
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
    let targetProgress = 0;
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

    function paint(progress, momentum) {
        cards.forEach((card, index) => {
            writeCardFrame(
                card,
                cardFrame(index, progress, geometry, momentum),
            );
            card.style.zIndex = String(10 + index);
        });

        const story = storyFrame(progress, viewportHeight, momentum);
        root.style.setProperty('--values-progress', story.progress.toFixed(5));
        root.style.setProperty('--values-heading-y', `${story.headingY.toFixed(2)}px`);
        root.style.setProperty('--values-heading-scale', story.headingScale.toFixed(4));
        root.style.setProperty('--values-heading-opacity', story.headingOpacity.toFixed(4));
        root.style.setProperty('--values-curve-y', `${story.curveY.toFixed(2)}px`);
        root.style.setProperty('--values-curve-opacity', story.curveOpacity.toFixed(4));
    }

    function render(time) {
        frame = 0;
        if (!active || destroyed || document.hidden) return;
        if (geometryDirty || !geometry) measure();

        targetProgress = readStoryProgress(root, viewportHeight);
        const delta = lastTime ? time - lastTime : FRAME_MS;
        lastTime = time;
        const snapshot = updateScrollMotion(
            motion,
            targetProgress,
            delta,
            snapNext,
        );

        paint(snapshot.visual, snapshot.momentum);
        snapNext = false;

        if (!snapshot.settled) requestRender();
        else lastTime = 0;
    }

    function onScroll() {
        targetProgress = readStoryProgress(root, viewportHeight);
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

    function onPageShow() {
        geometryDirty = true;
        snapNext = true;
        requestRender();
    }

    function onIntersection(entries) {
        const nextActive = entries.some((entry) => entry.isIntersecting);
        if (nextActive === active) return;

        active = nextActive;
        cancelFrame();
        if (!active) return;

        geometryDirty = true;
        targetProgress = readStoryProgress(root, window.innerHeight || 1);
        resetScrollMotion(motion, targetProgress);
        snapNext = true;
        requestRender();
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', onPageShow);
    document.addEventListener('visibilitychange', onVisibility);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '100% 0px 100% 0px',
            threshold: 0.01,
        });
        observer.observe(root);
    }

    targetProgress = readStoryProgress(root, viewportHeight);
    resetScrollMotion(motion, targetProgress);
    requestRender();

    return function destroy() {
        destroyed = true;
        active = false;
        cancelFrame();
        observer?.disconnect();
        window.removeEventListener('scroll', onScroll);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', onPageShow);
        document.removeEventListener('visibilitychange', onVisibility);
        root.classList.remove('is-values-ready');
        [
            '--values-progress', '--values-heading-y', '--values-heading-scale',
            '--values-heading-opacity', '--values-curve-y', '--values-curve-opacity',
        ]
            .forEach((name) => root.style.removeProperty(name));
        cards.forEach((card) => {
            MOTION_PROPERTIES.forEach((name) => card.style.removeProperty(name));
        });
    };
}

const valuesStory = document.querySelector('[data-values-story]');
if (valuesStory) createValuesStory(valuesStory);
