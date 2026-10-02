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
export async function prepareHeroShell({ signal } = {}) {
    const root = document.querySelector(SELECTORS.hero);
    if (root?.dataset.heroMode && root.dataset.heroControllerReady !== 'true') {
        await new Promise(resolve => root.addEventListener('schoolai:hero-controller-ready', resolve, { once: true, signal }));
    }
    const slide = root?.querySelector('[data-hero-slide].is-active');
    let image = slide?.querySelector('img');
    const poster = slide?.querySelector('[data-hero-video]')?.poster;
    if (!image && poster) { image = new Image(); image.src = poster; }
    if (!image) throw new Error('Required Hero visual unavailable');
    await image.decode?.();
    if (!image.complete || !image.naturalWidth) throw new Error('Required Hero visual unavailable');
    return { state: 'prepared' };
}
