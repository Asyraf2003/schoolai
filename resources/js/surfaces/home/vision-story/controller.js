import {
    createVisionTimeline,
    prepareVisionTypography,
} from './timeline.js';
import { createTypographyEntry } from './entry.js';
import { prepareVisionAssets } from './preparation.js';

const clamp = (value) => Math.max(0, Math.min(1, value));

export function mountVisionStory() {
    const root = document.querySelector('[data-vision-story]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const wide = window.matchMedia('(min-width: 1181px)');
    if (
        !root
        || reducedMotion.matches
        || typeof Element.prototype.animate !== 'function'
    ) return null;

    const track = root.querySelector('[data-vision-track]');
    let timeline = null;
    let observer = null;
    let resizeTimer = null;
    let frame = null;
    let prepared = false;
    let preparing = false;
    let near = false;
    let destroyed = false;
    let start = 0;
    let distance = 1;
    let targetProgress = 0;
    let renderedProgress = 0;
    let lastFrameTime = performance.now();
    const typographyEntry = createTypographyEntry(root, wide, () => timeline);

    function syncStoryHeight() {
        if (!track || wide.matches) {
            root.style.removeProperty('height');
            return;
        }
        root.style.height = `${Math.max(
            track.scrollHeight,
            window.innerHeight + 1,
        )}px`;
    }

    function updateProgressTarget() {
        targetProgress = clamp((window.scrollY - start) / distance);
    }

    function measure() {
        syncStoryHeight();
        const rect = root.getBoundingClientRect();
        start = window.scrollY + rect.top;
        distance = Math.max(1, root.offsetHeight - window.innerHeight);
        updateProgressTarget();
    }

    function render() {
        if (timeline) timeline.setProgress(renderedProgress);
    }

    function tick(now) {
        frame = null;
        if (destroyed || !near || !timeline) return;
        const elapsed = Math.min(64, Math.max(1, now - lastFrameTime));
        const alpha = 1 - Math.exp(-elapsed / 88);
        renderedProgress += (targetProgress - renderedProgress) * alpha;
        lastFrameTime = now;
        render();
        if (Math.abs(targetProgress - renderedProgress) > 0.00015) {
            frame = requestAnimationFrame(tick);
            return;
        }
        renderedProgress = targetProgress;
        render();
    }

    function scheduleFrame() {
        if (!near || !timeline || frame !== null) return;
        lastFrameTime = performance.now();
        frame = requestAnimationFrame(tick);
    }

    function updateTarget() {
        if (!prepared || !near || !timeline) return;
        updateProgressTarget();
        typographyEntry.sync();
        scheduleFrame();
    }

    function rebuildTimeline() {
        if (
            !prepared
            || destroyed
            || !root.classList.contains('is-enhanced')
        ) return;
        if (timeline) timeline.destroy();
        timeline = null;
        measure();
        timeline = createVisionTimeline(root);
        renderedProgress = targetProgress;
        render();
        typographyEntry.sync(true);
    }

    function finishStaticPreparation() {
        prepared = true;
        preparing = false;
        root.classList.remove('is-preparing');
    }

    async function prepare() {
        if (prepared || preparing || destroyed) return;
        preparing = true;
        const shouldEnhance = await prepareVisionAssets(
            root,
            prepareVisionTypography,
        );
        if (destroyed) return;
        if (!shouldEnhance) {
            finishStaticPreparation();
            return;
        }

        root.classList.add('is-enhanced');
        root.classList.remove('is-preparing');
        requestAnimationFrame(() => requestAnimationFrame(() => {
            if (destroyed) return;
            measure();
            timeline = createVisionTimeline(root);
            renderedProgress = targetProgress;
            render();
            prepared = true;
            preparing = false;
            typographyEntry.sync(true);
            scheduleFrame();
        }));
    }

    function onIntersection(entries) {
        near = entries.some((entry) => entry.isIntersecting);
        root.classList.toggle('is-near', near);
        if (near) {
            prepare();
            updateTarget();
        } else if (frame !== null) {
            cancelAnimationFrame(frame);
            frame = null;
        }
    }

    function onResize() {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(rebuildTimeline, 140);
    }

    function destroy() {
        if (destroyed) return;
        destroyed = true;
        window.clearTimeout(resizeTimer);
        if (frame !== null) cancelAnimationFrame(frame);
        if (observer) observer.disconnect();
        if (timeline) timeline.destroy();
        window.removeEventListener('scroll', updateTarget);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', rebuildTimeline);
        root.style.removeProperty('height');
        root.classList.remove('is-enhanced', 'is-near', 'is-preparing');
    }

    window.addEventListener('scroll', updateTarget, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', rebuildTimeline, { passive: true });
    window.addEventListener('pagehide', destroy, { once: true });

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '110% 0px 110% 0px',
            threshold: 0,
        });
        observer.observe(root);
    } else {
        near = true;
        root.classList.add('is-near');
    }

    prepare();
    return { destroy };
}
