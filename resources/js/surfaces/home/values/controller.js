import { clearHeadingClasses, createHeadingState,
    syncHeadingClasses, updateHeadingState } from './heading-state.js';
import { clearCenterMeasurement, collectValuesNodes,
    exposeCenterMeasurement, measureValuesGeometry } from './geometry.js';
import { clearValuesStory, paintValuesStory } from './paint.js';
import { mountValuesLifecycle } from './lifecycle.js';
import { FRAME_MS, createScrollMotion, resetScrollMotion,
    updateScrollMotion } from './motion.js';
import { createValuesSpatialBridge } from './spatial-controller.js';
import { readValuesFrameTarget, supportsStoryMotion } from './frame-target.js';

const VALUES_SPATIAL_ENABLED = false;

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
    const handoffMotion = createScrollMotion(0);
    const galleryHandoffMotion = createScrollMotion(0);
    let heading = createHeadingState(0);
    let frame = 0;
    let active = !('IntersectionObserver' in window);
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
        if (!frame && enabled && !destroyed && !document.hidden) {
            frame = window.requestAnimationFrame(render);
        }
    }

    const spatial = createValuesSpatialBridge(root, nodes.spatialHost, requestRender);
    spatial.setActive(active);

    function measure() {
        const previousMode = geometry?.mode;
        geometry = measureValuesGeometry(root, cards, nodes);
        exposeCenterMeasurement(root, geometry);
        geometryDirty = false;
        if (previousMode && previousMode !== geometry.mode) snapNext = true;
    }

    function render(time) {
        frame = 0;
        if (!enabled || destroyed || document.hidden) return;
        if (geometryDirty || !geometry) measure();

        const target = readValuesFrameTarget(root, nodes, geometry);
        const delta = lastTime ? time - lastTime : FRAME_MS;
        lastTime = time;
        const directScroll = geometry.mode < 4 || !active;
        let snapshot;

        if (directScroll) {
            resetScrollMotion(motion, target.story);
            snapshot = {
                visual: target.story,
                momentum: 0,
                settled: true,
            };
        } else {
            snapshot = updateScrollMotion(
                motion,
                target.story,
                delta,
                snapNext,
            );
        }

        const handoff = updateScrollMotion(
            handoffMotion,
            target.handoff,
            delta,
            true,
        );
        const galleryHandoff = updateScrollMotion(
            galleryHandoffMotion,
            target.galleryHandoff,
            delta,
            true,
        );
        const headingSnapshot = updateHeadingState(
            heading, target.story, target.headingTop,
            geometry.viewportHeight, time, geometry.mode >= 3,
        );

        syncHeadingClasses(root, headingSnapshot);
        paintValuesStory(
            root, cards, nodes, snapshot.visual, target.story, geometry,
            snapshot.momentum, headingSnapshot, handoff.visual,
            galleryHandoff.visual,
        );
        spatial.update({
            handoffProgress: handoff.visual,
            storyProgress: snapshot.visual,
            momentum: snapshot.momentum,
        });
        snapNext = false;

        if (active && (!snapshot.settled || !headingSnapshot.settled)) {
            requestRender();
        } else {
            lastTime = 0;
        }
    }

    function invalidateGeometry() {
        geometryDirty = true;
        requestRender();
    }

    function onVisibility() {
        if (document.hidden) {
            cancelFrame();
            spatial.suspend();
        } else {
            spatial.resume();
            invalidateGeometry();
        }
    }

    function onPageHide() {
        cancelFrame();
        spatial.suspend();
    }

    function onPageShow() {
        spatial.resume();
        invalidateGeometry();
    }

    function onIntersection(entries) {
        active = entries.some((entry) => entry.isIntersecting);
        spatial.setActive(active);
        root.classList.toggle('is-values-active', active && enabled);
        cancelFrame();
        geometryDirty = true;
        snapNext = true;
        requestRender();
    }

    function disable() {
        enabled = false;
        cancelFrame();
        spatial.setEnabled(false);
        root.classList.remove('is-values-ready', 'is-values-active');
        clearHeadingClasses(root);
        clearCenterMeasurement(root);
        clearValuesStory(root, cards, nodes);
        geometry = null;
    }

    function syncPreference() {
        const shouldEnable = capable && !reduced.matches;
        if (shouldEnable === enabled) return;
        if (!shouldEnable) return disable();

        enabled = true;
        heading = createHeadingState(0);
        root.classList.add('is-values-ready');
        root.classList.toggle('is-values-active', active);
        geometryDirty = true;
        snapNext = true;
        resetScrollMotion(motion, 0);
        resetScrollMotion(handoffMotion, 0);
        resetScrollMotion(galleryHandoffMotion, 0);
        spatial.setEnabled(VALUES_SPATIAL_ENABLED);
        requestRender();
    }

    const cleanupLifecycle = mountValuesLifecycle(root, nodes, {
        onIntersection,
        onPageHide,
        onPageShow,
        onPreference: syncPreference,
        onResize: invalidateGeometry,
        onScroll: requestRender,
        onVisibility,
        reduced,
    });

    syncPreference();

    return function destroy() {
        destroyed = true;
        disable();
        spatial.destroy();
        cleanupLifecycle();
    };
}

const valuesStory = document.querySelector('[data-values-story]');
if (valuesStory) createValuesStory(valuesStory);
