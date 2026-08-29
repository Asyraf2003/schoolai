import { createVisionGeometry } from './geometry.js';
import { prepareVisionAssets } from './preparation.js';
import { createVisionTimeline } from './timeline.js';

export function mountVisionStory() {
    const root = document.querySelector('[data-vision-story]');
    const wide = window.matchMedia('(min-width: 1024px)');
    if (!root) return null;

    const geometry = createVisionGeometry(root);
    let timeline = null;
    let observer = null;
    let resizeTimer = null;
    let frame = null;
    let prepared = false;
    let preparing = false;
    let near = false;
    let destroyed = false;
    let target = 0;
    let current = 0;
    let lastFrameTime = performance.now();

    function render() {
        timeline?.setProgress(current);
    }

    function tick(now) {
        frame = null;
        if (destroyed || !near || !timeline) return;
        const elapsed = Math.min(64, Math.max(1, now - lastFrameTime));
        const alpha = 1 - Math.exp(-elapsed / 88);
        current += (target - current) * alpha;
        lastFrameTime = now;
        render();

        if (Math.abs(target - current) > .00015) {
            frame = requestAnimationFrame(tick);
            return;
        }

        current = target;
        render();
    }

    function scheduleFrame() {
        if (!near || !timeline || frame !== null) return;
        lastFrameTime = performance.now();
        frame = requestAnimationFrame(tick);
    }

    function updateTarget() {
        if (!timeline || !wide.matches) return;
        target = geometry.readProgress();
        scheduleFrame();
    }

    async function prepare() {
        if (prepared || preparing || destroyed || !near || !wide.matches) return;
        preparing = true;
        await prepareVisionAssets(root);
        if (destroyed || !wide.matches) {
            preparing = false;
            return;
        }

        root.classList.add('is-enhanced');
        geometry.measure();
        timeline = createVisionTimeline(root);
        target = geometry.readProgress();
        current = target;
        prepared = true;
        preparing = false;
        render();
    }

    function disableEnhanced() {
        if (frame !== null) cancelAnimationFrame(frame);
        frame = null;
        timeline?.destroy();
        timeline = null;
        prepared = false;
        root.classList.remove('is-enhanced');
    }

    function syncMode() {
        if (!wide.matches) {
            disableEnhanced();
            return;
        }
        prepare();
        if (prepared) {
            geometry.measure();
            target = geometry.readProgress();
            current = target;
            render();
        }
    }

    function onResize() {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(syncMode, 140);
    }

    function onIntersection(entries) {
        near = entries.some((entry) => entry.isIntersecting);
        root.classList.toggle('is-near', near);
        if (near) {
            syncMode();
            updateTarget();
        } else if (frame !== null) {
            cancelAnimationFrame(frame);
            frame = null;
        }
    }

    function onPageShow(event) {
        if (event.persisted) syncMode();
    }

    function destroy(event) {
        if (event?.persisted || destroyed) return;
        destroyed = true;
        window.clearTimeout(resizeTimer);
        disableEnhanced();
        observer?.disconnect();
        root.classList.remove('is-near');
        window.removeEventListener('scroll', updateTarget);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', onPageShow);
        window.removeEventListener('pagehide', destroy);
    }

    window.addEventListener('scroll', updateTarget, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', onPageShow);
    window.addEventListener('pagehide', destroy);

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(onIntersection, {
            rootMargin: '0px 0px -5% 0px',
            threshold: 0,
        });
        observer.observe(root);
    } else {
        near = true;
        root.classList.add('is-near');
        syncMode();
    }

    return { destroy };
}
