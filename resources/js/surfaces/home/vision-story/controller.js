import { createVisionTimeline } from './timeline.js';
const clamp = (value) => Math.max(0, Math.min(1, value));
function decodeImages(root) {
    const images = Array.from(root.querySelectorAll('[data-vision-art]'));
    const work = Promise.allSettled(images.map((image) => {
        if (typeof image.decode !== 'function') return Promise.resolve();
        return image.decode();
    }));
    const timeout = new Promise((resolve) => window.setTimeout(resolve, 900));
    return Promise.race([work, timeout]);
}
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
    let entranceArmed = false;
    let start = 0;
    let distance = 1;
    let targetProgress = 0;
    let renderedProgress = 0;
    let lastFrameTime = performance.now();
    let lastScrollY = window.scrollY;
    let lastTop = Number.POSITIVE_INFINITY;
    function syncStoryHeight() {
        if (!track || wide.matches) {
            root.style.removeProperty('height');
            return;
        }
        root.style.height = `${Math.max(track.scrollHeight, window.innerHeight + 1)}px`;
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
    function syncTypographyEntry(initial = false) {
        if (!timeline) return;
        const top = root.getBoundingClientRect().top;
        const scrollY = window.scrollY;
        const movingDown = scrollY > lastScrollY + 0.5;
        const movingUp = scrollY < lastScrollY - 0.5;
        if (initial) {
            if (top > window.innerHeight) {
                timeline.resetTypography();
                entranceArmed = true;
            } else {
                timeline.showTypography();
                entranceArmed = false;
            }
        } else if (movingUp) {
            timeline.showTypography();
            entranceArmed = false;
        } else if (top > window.innerHeight) {
            if (!entranceArmed) timeline.resetTypography();
            entranceArmed = true;
        } else if (
            entranceArmed
            && movingDown
            && lastTop > window.innerHeight
            && top <= window.innerHeight
        ) {
            timeline.playTypography();
            entranceArmed = false;
        }
        lastScrollY = scrollY;
        lastTop = top;
    }
    function updateTarget() {
        if (!prepared || !near) return;
        updateProgressTarget();
        syncTypographyEntry();
        scheduleFrame();
    }
    function rebuildTimeline() {
        if (!prepared || destroyed) return;
        if (timeline) timeline.destroy();
        timeline = null;
        measure();
        timeline = createVisionTimeline(root);
        renderedProgress = targetProgress;
        render();
        syncTypographyEntry(true);
    }
    async function prepare() {
        if (prepared || preparing || destroyed) return;
        preparing = true;
        root.classList.add('is-preparing');
        await decodeImages(root);
        if (destroyed) return;
        root.classList.add('is-enhanced');
        root.classList.remove('is-preparing');
        requestAnimationFrame(() => {
            if (destroyed) return;
            measure();
            timeline = createVisionTimeline(root);
            renderedProgress = targetProgress;
            render();
            prepared = true;
            preparing = false;
            syncTypographyEntry(true);
            scheduleFrame();
        });
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
        prepare();
    }
    return { destroy };
}
