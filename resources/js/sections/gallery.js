import { loadGsapLibrary } from './gsap-loader.js';
import { createGalleryTimeline } from './gallery-timeline.js';
import { mountGalleryMedia } from './gallery-media.js';
import { mountGalleryHeading } from './gallery-heading.js';

export function mountGallery(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const heading = mountGalleryHeading(root);
    const media = mountGalleryMedia(root);
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    let timeline;
    let loading;
    let disposed = false;
    let suspended = false;
    let near = false;
    let version = 0;
    let width = innerWidth, height = innerHeight;
    let lastProgress = 0;
    const update = progress => {
        if (width === innerWidth && height === innerHeight) lastProgress = progress;
        media.sync();
    };
    const clear = (keepStage = false) => {
        version++;
        timeline?.scrollTrigger.kill(); timeline?.kill(); timeline = null;
        if (!keepStage) root.previousElementSibling?.removeAttribute('data-gallery-dark');
        if (!keepStage) {
            root.removeAttribute('data-gallery-enabled');
            delete root.dataset.galleryWidth; delete root.dataset.galleryHeight;
            delete root.dataset.galleryShrinkStart;
        }
        root.querySelectorAll('[data-gallery-item], [data-gallery-composition], [data-gallery-word]').forEach(node => node.removeAttribute('style'));
    };
    const prepare = async () => {
        if (!near || disposed || suspended || motion.matches || timeline || loading
            || root.querySelectorAll('[data-gallery-item]').length !== 10
            || !root.querySelector('[data-gallery-item] [data-gallery-video]')) return;
        const attempt = version;
        loading = true;
        try {
            const gsap = await loadGsapLibrary('gsap', lifecycle.signal);
            const trigger = await loadGsapLibrary('ScrollTrigger', lifecycle.signal);
            if (disposed || suspended || motion.matches || version !== attempt) return;
            root.setAttribute('data-gallery-enabled', '');
            timeline = createGalleryTimeline(gsap, trigger, root, update);
            trigger.refresh();
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
        const progress = lastProgress;
        width = innerWidth; height = innerHeight;
        if (!timeline) return;
        const active = progress > 0 && progress < 1;
        clear(true);
        timeline = createGalleryTimeline(window.gsap, window.ScrollTrigger, root, update);
        window.ScrollTrigger.refresh();
        if (active) scrollTo(0, timeline.scrollTrigger.start + progress * (timeline.scrollTrigger.end - timeline.scrollTrigger.start));
        timeline.scrollTrigger.update();
    }, { signal: lifecycle.signal });
    return {
        suspend() { suspended = true; timeline?.scrollTrigger.disable(false); media.suspend(); heading.suspend(); },
        resume() { suspended = false; timeline?.scrollTrigger.enable(false, false); prepare(); media.resume(); heading.resume(); },
        dispose() { disposed = true; clear(); observer?.disconnect(); lifecycle.abort(); media.dispose(); heading.dispose(); },
    };
}
