import { loadGsapLibrary } from './gsap-loader.js';
import { createValuesLineTimeline } from './values-line-timeline.js';

export function mountValuesLine(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const scene = root.querySelector('[data-values-line]');
    const paths = [...scene.querySelectorAll('[data-values-line-path]')];
    const originals = paths.map(path => path.getAttribute('d'));
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    let loading;
    let timeline;
    let observer;
    let near = false;
    let suspended = false;
    let disposed = false;
    let running = false;
    const synchronize = () => {
        const trigger = timeline?.scrollTrigger;
        if (!trigger) return;
        // ScrollTrigger clamps progress outside the scene; no offscreen playback.
        const shouldRun = !suspended && !document.hidden;
        if (shouldRun === running) return;
        running = shouldRun;
        if (!running) trigger.disable(false);
        else { trigger.enable(false, false); window.ScrollTrigger.refresh(); trigger.update(); }
    };
    const clear = () => {
        loading?.abort(); loading = null;
        timeline?.scrollTrigger.kill(); timeline?.kill(); timeline = null;
        observer?.disconnect(); observer = null;
        paths.forEach((path, index) => {
            path.removeAttribute('style');
            path.setAttribute('d', originals[index]);
        });
        scene.querySelector('svg').setAttribute('viewBox', '0 0 1920 5400');
        root.removeAttribute('data-line-ready');
        near = false; running = false;
    };
    const prepare = async () => {
        if (loading || timeline || suspended || document.hidden || motion.matches || disposed || !near) return;
        const attempt = new AbortController();
        loading = attempt;
        try {
            const gsap = await loadGsapLibrary('gsap', attempt.signal);
            const ScrollTrigger = await loadGsapLibrary('ScrollTrigger', attempt.signal);
            if (attempt.signal.aborted || disposed || motion.matches) return;
            root.setAttribute('data-line-ready', '');
            timeline = createValuesLineTimeline(gsap, ScrollTrigger, scene, paths, originals);
            running = true;
            synchronize();
        } catch {
            // The existing title/color surface remains usable without decoration.
            if (timeline) return;
            root.removeAttribute('data-line-ready');
            paths.forEach(path => path.removeAttribute('style'));
        } finally {
            if (loading === attempt) {
                loading = null;
                if (attempt.signal.aborted) prepare();
            }
        }
    };
    const configure = () => {
        clear();
        if (disposed || motion.matches || !('IntersectionObserver' in window) || !paths.every(path => path.getTotalLength)) return;
        observer = new IntersectionObserver(entries => {
            near = entries.some(entry => entry.isIntersecting);
            prepare(); synchronize();
        }, { rootMargin: '150% 0px' });
        observer.observe(root);
    };
    const visibility = () => {
        if (document.hidden) loading?.abort();
        else prepare();
        synchronize();
    };
    motion.addEventListener('change', configure, { signal: lifecycle.signal });
    document.addEventListener('visibilitychange', visibility, { signal: lifecycle.signal });
    window.addEventListener('resize', () => {
        if (!timeline) return;
        timeline.scrollTrigger.kill(); timeline.kill();
        timeline = createValuesLineTimeline(window.gsap, window.ScrollTrigger, scene, paths, originals);
        running = true; synchronize();
    }, { signal: lifecycle.signal });
    configure();
    return {
        suspend() { suspended = true; loading?.abort(); synchronize(); },
        resume() { suspended = false; prepare(); synchronize(); if (running) window.ScrollTrigger.refresh(); },
        dispose() { disposed = true; clear(); lifecycle.abort(); },
    };
}
