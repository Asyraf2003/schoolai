import { collectProgramDom } from '../../surfaces/home/program-journey/geometry.js';
import { createProgramDialogIntegration } from '../../surfaces/home/program-journey/integration.js';
import { mountReduced } from '../../surfaces/home/program-journey/reduced-controller.js';

const SELECTORS = { hero: '[data-hero-slider]', vision: '[data-vision-story]', program: '[data-program-kinetic]', values: '[data-values-story]', gallery: '[data-gallery-story]' };
export function usableOpeningUnit(unit) {
    const root = document.querySelector(SELECTORS[unit]);
    if (!root || !root.textContent.trim() || root.getBoundingClientRect().height <= 0) return false;
    const style = window.getComputedStyle(root);
    return style.display !== 'none' && style.visibility !== 'hidden';
}
export function installOpeningFallback(unit) {
    const root = document.querySelector(SELECTORS[unit]);
    if (unit === 'hero' && root) {
        root.dataset.heroPreparationFallback = 'true';
        document.dispatchEvent(new CustomEvent('schoolai:hero-media-fallback'));
    }
    if (unit === 'program' && root && !['gsap', 'static-fallback'].includes(root.dataset.programReady)) {
        const dom = collectProgramDom(root);
        if (!dom.triggers.length || !dom.layer || dom.details.length !== dom.triggers.length) return false;
        root.classList.add('is-enhanced');
        const cleanup = mountReduced(dom, createProgramDialogIntegration(dom));
        root.dataset.programReady = 'static-fallback';
        window.addEventListener('pagehide', event => { if (!event.persisted) cleanup(); });
    }
    return usableOpeningUnit(unit);
}
export function prepareHeroActiveMedia({ signal } = {}) {
    const root = document.querySelector(SELECTORS.hero);
    const slide = root?.querySelector('[data-hero-slide].is-active');
    const video = slide?.querySelector('[data-hero-video]');
    const image = slide?.querySelector('img');
    if (!video) {
        return Promise.resolve(image?.decode?.()).then(() => ({
            state: image?.complete && image.naturalWidth > 0 ? 'prepared' : 'static-fallback',
        }));
    }
    return new Promise(resolve => {
        const listeners = new AbortController();
        const options = { signal: listeners.signal };
        function finish(state) {
            listeners.abort();
            signal?.removeEventListener('abort', fallback);
            resolve({ state });
        }
        const fallback = () => finish('static-fallback');
        const check = () => {
            if (video.readyState >= 2 || root.dataset.heroFirstFrame === 'true') finish('prepared');
            else if (['static-reduced', 'autoplay-blocked', 'media-error'].includes(root.dataset.heroMediaState)) fallback();
        };
        video.addEventListener('loadeddata', check, options);
        video.addEventListener('error', fallback, options);
        video.addEventListener('schoolai:hero-media-state', check, options);
        window.matchMedia('(prefers-reduced-motion: reduce)').addEventListener('change', check, options);
        signal?.addEventListener('abort', fallback, { once: true });
        if (signal?.aborted) fallback();
        else check();
    });
}
