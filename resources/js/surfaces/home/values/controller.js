import { clearHeadingClasses, createHeadingState,
    syncHeadingClasses, updateHeadingState } from './heading-state.js';
import { clearCenterMeasurement, collectValuesNodes,
    exposeCenterMeasurement, measureValuesGeometry } from './geometry.js';
import { clearValuesStory, paintValuesStory } from './paint.js';
import { FRAME_MS, createScrollMotion, readStoryProgress,
    resetScrollMotion, updateScrollMotion } from './motion.js';
function supportsStoryMotion() {
    return typeof CSS !== 'undefined'
        && CSS.supports('overflow', 'clip')
        && CSS.supports('position', 'sticky')
        && CSS.supports('perspective', '800px')
        && CSS.supports('transform-style', 'preserve-3d');
}

export function createValuesStory(root) {
    const cards = Array.from(root.querySelectorAll('[data-values-card]'));
    if (cards.length !== 4) return () => {};

    let nodes;
    try {
        nodes = collectValuesNodes(root);
    } catch {
        return () => {};
    }

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    const capable = supportsStoryMotion();
    const motion = createScrollMotion(0);
    let heading = createHeadingState(0);
    let frame = 0;
    let observer = null;
    let resizeObserver = null;
    let active = true;
    let enabled = false;
    let destroyed = false;
    let geometryDirty = true;
    let snapNext = true;
    let lastTime = 0;
    let geometry = null;

    function cancelFrame() {
        if (frame) window.cancelAnimationFrame(frame);
        frame = 0;
        lastTime = 0;
    }

    function requestRender() {
        if (!frame && enabled && active && !destroyed && !document.hidden) {
            frame = window.requestAnimationFrame(render);
        }
    }

    function measure() {
        const previousMode = geometry?.mode;
        geometry = measureValuesGeometry(root, cards, nodes);
        exposeCenterMeasurement(root, geometry);
        geometryDirty = false;

        if (previousMode && previousMode !== geometry.mode) snapNext = true;
    }

    function readFrameTarget() {
        const timelineTop = nodes.timeline.getBoundingClientRect().top;
        const storyTop = geometry.mode === 4
            ? timelineTop
            : root.getBoundingClientRect().top;

        return {
            target: readStoryProgress(storyTop, geometry),
            timelineTop,
        };
    }

    function render(time) {
        frame = 0;
        if (!enabled || !active || destroyed || document.hidden) return;
        if (geometryDirty || !geometry) measure();

        const frameTarget = readFrameTarget();
        const delta = lastTime ? time - lastTime : FRAME_MS;
        lastTime = time;
        const snapshot = updateScrollMotion(
            motion,
            frameTarget.target,
            delta,
            snapNext,
        );
        const headingSnapshot = updateHeadingState(
            heading,
            frameTarget.target,
            frameTarget.timelineTop,
            geometry.viewportHeight,
            time,
            geometry.mode === 4,
        );

        syncHeadingClasses(root, headingSnapshot);
        paintValuesStory(
            root,
            cards,
            nodes,
            snapshot.visual,
            geometry,
            snapshot.momentum,
            headingSnapshot,
        );
        snapNext = false;

        if (!snapshot.settled || !headingSnapshot.settled) requestRender();
        else lastTime = 0;
    }

    function invalidateGeometry() {
        geometryDirty = true;
        requestRender();
    }

    function onVisibility() {
        if (document.hidden) cancelFrame();
        else invalidateGeometry();
    }

    function onIntersection(entries) {
        active = entries.some((entry) => entry.isIntersecting);
        root.classList.toggle('is-values-active', active && enabled);
        cancelFrame();
        if (!active) return;
        geometryDirty = true;
        snapNext = true;
        requestRender();
    }

    function disable() {
        enabled = false;
        cancelFrame();
        root.classList.remove('is-values-ready', 'is-values-active');
        clearHeadingClasses(root);
        clearCenterMeasurement(root);
        clearValuesStory(root, cards, nodes);
        geometry = null;
    }

    function syncPreference() {
        const shouldEnable = capable && !reduced.matches;
        if (shouldEnable === enabled) return;
        if (!shouldEnable) {
            disable();
            return;
        }

        enabled = true;
        heading = createHeadingState(0);
        root.classList.add('is-values-ready');
        root.classList.toggle('is-values-active', active);
        geometryDirty = true;
        snapNext = true;
        resetScrollMotion(motion, 0);
        requestRender();
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', invalidateGeometry, { passive: true });
    window.addEventListener('pageshow', invalidateGeometry);
    window.addEventListener('pagehide', cancelFrame);
    document.addEventListener('visibilitychange', onVisibility);
    reduced.addEventListener('change', syncPreference);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '60% 0px 60% 0px',
            threshold: 0.01,
        });
        observer.observe(root);
    }

    if ('ResizeObserver' in window) {
        resizeObserver = new ResizeObserver(invalidateGeometry);
        resizeObserver.observe(nodes.stage);
        resizeObserver.observe(nodes.grid);
    }

    syncPreference();

    return function destroy() {
        destroyed = true;
        disable();
        observer?.disconnect();
        resizeObserver?.disconnect();
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', invalidateGeometry);
        window.removeEventListener('pageshow', invalidateGeometry);
        window.removeEventListener('pagehide', cancelFrame);
        document.removeEventListener('visibilitychange', onVisibility);
        reduced.removeEventListener('change', syncPreference);
    };
}

const valuesStory = document.querySelector('[data-values-story]');
if (valuesStory) createValuesStory(valuesStory);
