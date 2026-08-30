import {
    fitGalleryStoryVisuals,
    galleryStoryVisual,
} from './welcome/gallery-story-visual.js';
import { armGalleryPattern } from './welcome/gallery-pattern.js';

let mounted = false;
const BLUE = [32, 56, 255];
const GALLERY = [111, 155, 114];
const clamp = (value) => Math.max(0, Math.min(1, value));
const clampSigned = (value) => Math.max(-1, Math.min(1, value));

function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - (2 * progress));
}

function mixColor(from, to, progress) {
    const value = smoothstep(progress);
    const channels = from.map((channel, index) => Math.round(
        channel + ((to[index] - channel) * value),
    ));
    return `rgb(${channels.join(' ')})`;
}

function readValuesExitProgress(valuesWorld) {
    if (!valuesWorld) return 0;
    const raw = getComputedStyle(valuesWorld)
        .getPropertyValue('--values-gallery-exit-progress').trim();
    const value = Number.parseFloat(raw);
    return Number.isFinite(value) ? clamp(value) : 0;
}

function safePlay(video) {
    const attempt = video.play();
    if (attempt && typeof attempt.catch === 'function') {
        attempt.catch(() => {});
    }
}

function mountGalleryVideoPreviews(root) {
    const previews = Array.from(root.querySelectorAll('[data-gallery-video-preview]'));
    const activePreviews = new Set();
    let observer = null;

    if (!previews.length) return () => {};

    function hydrate(preview) {
        if (preview.dataset.galleryVideoHydrated === 'true') return;
        const source = preview.dataset.galleryVideoSrc || '';
        if (!source) return;

        preview.dataset.galleryVideoHydrated = 'true';
        preview.muted = true;
        preview.defaultMuted = true;
        preview.loop = true;
        preview.playsInline = true;
        preview.src = source;
        preview.load();
    }

    function play(preview) {
        hydrate(preview);
        if (!document.hidden) safePlay(preview);
    }

    previews.forEach((preview) => {
        preview.addEventListener('loadeddata', () => {
            preview.classList.add('is-ready');
        }, { once: true });
    });

    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                const preview = entry.target;
                if (entry.isIntersecting) {
                    activePreviews.add(preview);
                    play(preview);
                } else {
                    activePreviews.delete(preview);
                    preview.pause();
                }
            });
        }, {
            rootMargin: '180px 0px',
            threshold: 0.01,
        });

        previews.forEach((preview) => observer.observe(preview));
    } else {
        previews.forEach((preview) => {
            activePreviews.add(preview);
            play(preview);
        });
    }

    function onVisibilityChange() {
        if (document.hidden) {
            previews.forEach((preview) => preview.pause());
            return;
        }

        activePreviews.forEach(play);
    }

    document.addEventListener('visibilitychange', onVisibilityChange);

    return () => {
        observer?.disconnect();
        activePreviews.clear();
        previews.forEach((preview) => preview.pause());
        document.removeEventListener('visibilitychange', onVisibilityChange);
    };
}

function mountGalleryStory(root) {
    if (mounted) return;
    mounted = true;

    const section = root.closest('.galeri-section') || root;
    const page = section.closest('.home-page');
    const items = Array.from(root.querySelectorAll('[data-gallery-story-item]'));
    const finalBackground = items[items.length - 1]?.dataset.galleryBackground || '';
    const valuesWorld = document.querySelector('[data-program-values-world]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const cleanPattern = armGalleryPattern(section);
    let cleanVideoPreviews = () => {};
    let activeBackground = '';
    let frame = 0;
    let destroyed = false;

    if (page && finalBackground) {
        page.style.setProperty('--gallery-story-final-bg', finalBackground);
    }

    function paintHandoff() {
        const exitProgress = readValuesExitProgress(valuesWorld);
        const sectionTop = section.getBoundingClientRect().top;
        const desktop = window.innerWidth >= 1280;
        const active = desktop && !reducedMotion.matches
            && exitProgress > 0.0001 && sectionTop > 1;

        section.classList.toggle('is-gallery-handoff', active);
        root.style.setProperty(
            '--gallery-handoff-color', mixColor(BLUE, GALLERY, exitProgress),
        );
        root.style.setProperty('--gallery-handoff-opacity', active ? '1' : '0');
        root.style.setProperty(
            '--gallery-title-opacity',
            active ? smoothstep((exitProgress - 0.68) / 0.32).toFixed(4) : '1',
        );
        return active;
    }

    function paintItems() {
        if (reducedMotion.matches) return;

        const viewportHeight = Math.max(window.innerHeight, 1);
        const viewportCenter = viewportHeight * 0.5;
        const revealStart = viewportHeight * 0.98;
        const revealDistance = viewportHeight * 0.62;
        let nearestDistance = Number.POSITIVE_INFINITY;
        let nearestBackground = '';

        items.forEach((item) => {
            const target = galleryStoryVisual(item);
            if (!target) return;
            const { media, visual } = target;
            const boundRect = media.getBoundingClientRect();
            const visualHeight = visual.offsetHeight || visual.getBoundingClientRect().height;
            const visualTop = boundRect.top + ((boundRect.height - visualHeight) * 0.5);
            const raw = clamp((revealStart - visualTop) / revealDistance);
            const entrance = smoothstep(raw);
            const copyProgress = smoothstep(clamp((raw - 0.12) / 0.88));
            const mediaCenter = visualTop + (visualHeight * 0.5);
            const centerDelta = mediaCenter - viewportCenter;
            const signed = clampSigned(centerDelta / (viewportHeight * 0.52));
            const openness = smoothstep(1 - Math.abs(signed));
            const closingInset = 40 * (1 - openness);
            const topInset = signed >= 0 ? closingInset : 0;
            const bottomInset = signed < 0 ? closingInset : 0;
            const mediaY = (1 - entrance) * Math.min(190, viewportHeight * 0.22);
            const copyY = (1 - copyProgress) * Math.min(108, viewportHeight * 0.125);
            const distance = Math.abs(centerDelta);

            if (distance < nearestDistance) {
                nearestDistance = distance;
                nearestBackground = item.dataset.galleryBackground || '';
            }
            visual.style.setProperty('--gallery-media-y', `${mediaY.toFixed(2)}px`);
            visual.style.setProperty('--gallery-media-opacity', entrance.toFixed(4));
            visual.style.setProperty('--gallery-window-top', `${topInset.toFixed(3)}%`);
            visual.style.setProperty('--gallery-window-bottom', `${bottomInset.toFixed(3)}%`);
            item.style.setProperty('--gallery-copy-y', `${copyY.toFixed(2)}px`);
            item.style.setProperty('--gallery-copy-opacity', copyProgress.toFixed(4));
        });

        if (nearestBackground && nearestBackground !== activeBackground) {
            activeBackground = nearestBackground;
            section.style.setProperty('--gallery-story-bg', nearestBackground);
        }
    }

    function render() {
        frame = 0;
        if (destroyed || document.hidden) return;
        const handoffActive = paintHandoff();
        paintItems();
        if (handoffActive) frame = window.requestAnimationFrame(render);
    }

    function requestRender() {
        if (!frame && !destroyed) frame = window.requestAnimationFrame(render);
    }

    function paintStatic() {
        section.classList.remove('is-gallery-handoff');
        root.style.setProperty('--gallery-title-opacity', '1');
        items.forEach((item) => {
            const target = galleryStoryVisual(item);
            if (target) {
                target.visual.style.setProperty('--gallery-media-y', '0px');
                target.visual.style.setProperty('--gallery-media-opacity', '1');
                target.visual.style.setProperty('--gallery-window-top', '0%');
                target.visual.style.setProperty('--gallery-window-bottom', '0%');
            }
            item.style.setProperty('--gallery-copy-y', '0px');
            item.style.setProperty('--gallery-copy-opacity', '1');
        });
    }

    function onMotionChange() {
        if (reducedMotion.matches) paintStatic();
        else requestRender();
    }

    function onResize() {
        fitGalleryStoryVisuals(items, requestRender);
        requestRender();
    }

    function destroy(event) {
        if (event?.persisted) return;
        destroyed = true;
        if (frame) window.cancelAnimationFrame(frame);
        cleanPattern();
        cleanVideoPreviews();
        window.removeEventListener('scroll', requestRender);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pageshow', requestRender);
        window.removeEventListener('pagehide', destroy);
        reducedMotion.removeEventListener?.('change', onMotionChange);
    }

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pageshow', requestRender);
    window.addEventListener('pagehide', destroy);
    reducedMotion.addEventListener?.('change', onMotionChange);
    fitGalleryStoryVisuals(items, requestRender);
    cleanVideoPreviews = mountGalleryVideoPreviews(root);

    if (reducedMotion.matches) paintStatic();
    else {
        paintHandoff();
        paintItems();
    }
    section.classList.add('is-gallery-enhanced');
    requestRender();
}

export function prepareHomepageDepthGallery() {
    const root = document.querySelector('[data-gallery-story]');
    if (!root) return Promise.resolve(null);
    mountGalleryStory(root);
    return Promise.resolve(root);
}
