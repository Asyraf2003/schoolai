import { loadGsapLibrary } from './gsap-loader.js';
import { createGalleryTimeline } from './gallery-timeline.js';
import { mountGalleryMedia } from './gallery-media.js';
import { mountGalleryEntry } from './gallery-entry.js';

export function mountGallery(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    let entry;
    const media = mountGalleryMedia(root);
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let timeline;
    let loading;
    let disposed = false;
    let suspended = false;
    let near = false;
    let version = 0;
    let width = innerWidth, height = innerHeight;
    const preceding = root.previousElementSibling;
    let precedingHeight = preceding?.offsetHeight;
    let lastProgress = 0;
    let resizeIntent;
    let resizeState;
    let reflowFrame = 0;
    const update = progress => {
        if (!resizeState && width === innerWidth && height === innerHeight && precedingHeight === preceding?.offsetHeight) {
            lastProgress = progress;
        }
        media.sync();
    };
    const clear = (keepStage = false) => {
        version++;
        timeline?.scrollTrigger.kill(); timeline?.kill(); timeline = null;
        if (!keepStage) {
            root.previousElementSibling?.removeAttribute('data-gallery-dark');
            root.previousElementSibling?.style.removeProperty('--gallery-dark-pct');
            root.previousElementSibling?.style.removeProperty('--gallery-decoration-opacity');
            root.previousElementSibling?.style.removeProperty('--gallery-line-opacity');
            root.style.removeProperty('--gallery-dark-pct');
            root.style.removeProperty('--gallery-opening-gap-extra');
        }
        if (!keepStage) {
            cancelAnimationFrame(reflowFrame); reflowFrame = 0; resizeState = null; resizeIntent = null;
            root.removeAttribute('data-gallery-enabled');
            delete root.dataset.galleryWidth; delete root.dataset.galleryHeight;
            delete root.dataset.galleryShrinkStart;
            root.removeAttribute('data-gallery-reflow-pending');
        }
        root.querySelectorAll('[data-gallery-item], [data-gallery-composition], [data-gallery-word]').forEach(node => node.removeAttribute('style'));
    };
    const refreshAt = (nativeScroll, progress = null) => {
        window.ScrollTrigger.refresh();
        const trigger = timeline.scrollTrigger;
        scrollTo(0, progress === null ? nativeScroll : trigger.start + progress * (trigger.end - trigger.start));
        trigger.update();
    };
    const prepare = async () => {
        if (!near || disposed || suspended || motion.matches || timeline || loading
            || root.querySelectorAll('[data-gallery-item]').length !== 10) return;
        const attempt = version;
        loading = true;
        try {
            const gsap = await loadGsapLibrary('gsap', lifecycle.signal);
            const trigger = await loadGsapLibrary('ScrollTrigger', lifecycle.signal);
            if (disposed || suspended || motion.matches || version !== attempt) return;
            const nativeScroll = scrollY;
            entry.measure();
            root.setAttribute('data-gallery-enabled', '');
            timeline = createGalleryTimeline(gsap, trigger, root, update);
            refreshAt(nativeScroll);
        } catch { clear(); }
        finally { loading = false; }
    };
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        near = entries.some(entry => entry.isIntersecting); prepare();
    }, { rootMargin: '200% 0px' }) : null;
    observer?.observe(root);
    const configure = () => { clear(); prepare(); media.sync(); };
    motion.addEventListener('change', configure, { signal: lifecycle.signal });
    window.addEventListener('resize', () => {
        if (width === innerWidth && height === innerHeight) return;
        const box = root.getBoundingClientRect();
        resizeIntent = { scroll: scrollY, progress: lastProgress > 0 && lastProgress < 1
            && box.top < innerHeight && box.bottom > 0 ? lastProgress : null };
    }, { capture: true, signal: lifecycle.signal });
    window.addEventListener('resize', () => {
        if (width === innerWidth && height === innerHeight) return;
        width = innerWidth; height = innerHeight;
        resizeState = { ...(resizeIntent ?? { scroll: scrollY, progress: null }), expected: scrollY };
        resizeIntent = null;
        scheduleReflow();
    }, { signal: lifecycle.signal });
    const reconcile = () => {
        reflowFrame = 0;
        root.removeAttribute('data-gallery-reflow-pending');
        if (suspended || disposed) return;
        const entryChanged = entry?.measure();
        const precedingChanged = precedingHeight !== preceding?.offsetHeight;
        precedingHeight = preceding?.offsetHeight;
        if (!timeline) { resizeState = null; return; }
        const signature = `${width}:${height}:${precedingHeight}:${root.offsetTop}:${root.offsetHeight}`;
        const state = resizeState;
        if (state && Math.abs(scrollY - state.expected) >= 2) {
            state.progress = null; state.scroll = scrollY;
        }
        if (state?.signature === signature) {
            resizeState = null; update(timeline.scrollTrigger.progress); return;
        }
        if (!state && !precedingChanged && !entryChanged) return;
        if (+root.dataset.galleryWidth !== width || +root.dataset.galleryHeight !== height) {
            clear(true);
            timeline = createGalleryTimeline(window.gsap, window.ScrollTrigger, root, update);
        }
        refreshAt(state?.scroll ?? scrollY, state?.progress ?? null);
        if (state) {
            state.signature = signature; state.expected = scrollY;
            scheduleReflow();
        }
    };
    const scheduleReflow = () => {
        if (reflowFrame || suspended || disposed) return;
        root.setAttribute('data-gallery-reflow-pending', '');
        reflowFrame = requestAnimationFrame(reconcile);
    };
    const reflow = typeof ResizeObserver === 'function' ? new ResizeObserver(() => {
        if (precedingHeight !== preceding?.offsetHeight) scheduleReflow();
    }) : null;
    if (preceding) reflow?.observe(preceding);
    entry = mountGalleryEntry(root, scheduleReflow);
    entry.measure();
    return {
        suspend() { suspended = true; cancelAnimationFrame(reflowFrame); reflowFrame = 0; timeline?.scrollTrigger.disable(false); media.suspend(); entry.suspend(); },
        resume() { suspended = false; timeline?.scrollTrigger.enable(false, false); reconcile(); prepare(); media.resume(); entry.resume(); },
        dispose() {
            disposed = true; cancelAnimationFrame(reflowFrame); clear();
            observer?.disconnect(); reflow?.disconnect(); lifecycle.abort(); media.dispose(); entry.dispose();
        },
    };
}
