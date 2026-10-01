import { subscribeHomepageFrame } from './welcome/scroll-frame.js';
import { readGalleryItems } from './welcome/gallery-story-frame.js';
import {
    fitGalleryStoryVisuals,
    galleryStoryVisual,
} from './welcome/gallery-story-visual.js';
import { armGalleryPattern } from './welcome/gallery-pattern.js';
import { mountGalleryVideoPreviews } from './welcome/gallery-video-preview.js';
let mounted = false;
let preparationPromise = null;
const BLUE = [32, 56, 255];
const GALLERY = [111, 155, 114];
const clamp = (value) => Math.max(0, Math.min(1, value));
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
    const cached = valuesWorld.dataset.valuesGalleryExitProgress;
    if (cached !== undefined) return clamp(Number.parseFloat(cached) || 0);
    const raw = getComputedStyle(valuesWorld)
        .getPropertyValue('--values-gallery-exit-progress').trim();
    const value = Number.parseFloat(raw);
    return Number.isFinite(value) ? clamp(value) : 0;
}
function mountGalleryStory(root, signal) {
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
    let destroyed = false;
    if (page && finalBackground) {
        page.style.setProperty('--gallery-story-final-bg', finalBackground);
    }
    function paintHandoff({ exitProgress, sectionTop }) {
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
    function paintItems({ frames, nearestBackground }) {
        frames.forEach(({ item, visual, mediaY, entrance, topInset, bottomInset, copyY, copyProgress }) => {
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
    let resolveReady;
    const ready = new Promise(resolve => { resolveReady = resolve; });
    const shared = subscribeHomepageFrame(() => ({
        exitProgress: readValuesExitProgress(valuesWorld),
        sectionTop: section.getBoundingClientRect().top,
        items: readGalleryItems(reducedMotion.matches ? [] : items),
    }), snapshot => {
        if (destroyed) return;
        paintHandoff(snapshot);
        paintItems(snapshot.items);
        section.classList.add('is-gallery-enhanced');
        root.dataset.galleryReady = reducedMotion.matches ? 'static-fallback' : 'prepared';
        resolveReady({ state: root.dataset.galleryReady });
    });
    function requestRender() { if (!destroyed) shared.request(); }
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
        shared.remove();
        resolveReady({ state: 'disposed' });
        cleanPattern();
        cleanVideoPreviews();
        valuesWorld?.removeEventListener('schoolai:values-frame', requestRender);
        window.removeEventListener('resize', onResize);
        window.removeEventListener('pagehide', destroy);
        reducedMotion.removeEventListener?.('change', onMotionChange);
    }
    valuesWorld?.addEventListener('schoolai:values-frame', requestRender);
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('pagehide', destroy);
    reducedMotion.addEventListener?.('change', onMotionChange);
    fitGalleryStoryVisuals(items, requestRender);
    cleanVideoPreviews = mountGalleryVideoPreviews(root);
    if (reducedMotion.matches) paintStatic();

    const fallback = () => { destroy(); paintStatic(); root.dataset.galleryReady = 'static-fallback'; };
    signal?.addEventListener('abort', fallback, { once: true });
    if (signal?.aborted) fallback();
    else requestRender();
    return ready;
}
export function prepareHomepageDepthGallery({ signal } = {}) {
    const root = document.querySelector('[data-gallery-story]');
    if (!root) return Promise.resolve(null);
    preparationPromise ||= mountGalleryStory(root, signal);
    return preparationPromise;
}
