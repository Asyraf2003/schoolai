import { createHomepageScrollGate } from './scroll-gate.js';
import { createOpeningLedger, REQUIRED_OPENING_UNITS } from './opening-ledger.js';
import { createOpeningProgress } from './opening-progress.js';
import { installOpeningFallback, prepareHeroShell, usableOpeningUnit } from './opening-readiness.js';
import { prepareHomepageFonts, prepareHomepageImages, prepareHomepageStyles, prepareHomepageGeometry, yieldHomepageWork } from './homepage-assets.js';
import { prepareHomepageNavigation, prepareHomepageMedia, preparedSemanticSurface } from './homepage-readiness.js';

export const HOME_PREPARATION_ORDER = REQUIRED_OPENING_UNITS;
const HERO_READY_EVENT = 'schoolai:hero-ready';
const preparationSignal = new AbortController();
let scrollGate = null;
let preparationPromise = null;
let scheduled = false;
let disposed = false;
let modules;
async function prepareRuntimes() {
    modules = await Promise.all([
        import('../welcome-vision-story.js'),
        import('../../surfaces/home/program-values-world.js'),
        import('./program-cards.js'),
        import('../../surfaces/home/values/preparation.js'),
        import('../welcome-depth-gallery.js'),
        import('./testimonial-wall.js'),
    ]);
    document.querySelector('#galeri')?.classList.add('is-gallery-pattern-ready');
    return { state: 'prepared' };
}
const preparationSteps = {
    hero: () => prepareHeroShell({ signal: preparationSignal.signal }),
    navigation: prepareHomepageNavigation,
    runtimes: prepareRuntimes,
    styles: prepareHomepageStyles,
    fonts: prepareHomepageFonts,
    images: prepareHomepageImages,
    media: () => prepareHomepageMedia(preparationSignal.signal),
    vision: () => modules[0].prepareHomepageVisionStory({ signal: preparationSignal.signal }),
    program: () => {
        modules[1].mountProgramValuesWorld(document.querySelector('[data-program-values-world]'), { signal: preparationSignal.signal });
        return modules[2].prepareHomepageProgram({ signal: preparationSignal.signal });
    },
    values: () => modules[3].prepareHomepageValues({ signal: preparationSignal.signal }),
    gallery: () => modules[4].prepareHomepageDepthGallery({ signal: preparationSignal.signal }),
    testimonials: () => modules[5].prepareHomepageTestimonials(),
    articles: () => preparedSemanticSurface('[data-article-showcase]'),
    footer: () => preparedSemanticSurface('.site-footer'),
    geometry: prepareHomepageGeometry,
};
function yieldToBrowser() { return yieldHomepageWork(); }
async function runPreparation() {
    const ledger = createOpeningLedger();
    const progress = createOpeningProgress(scrollGate, ledger);
    const root = document.documentElement;
    root.dataset.homePreparationState = 'preparing';
    for (const section of HOME_PREPARATION_ORDER) {
        if (disposed) return;
        await yieldToBrowser();
        let result;
        try {
            result = await preparationSteps[section]();
        } catch (error) {
            if (disposed) return;
            root.dataset.homePreparationState = 'failed';
            root.dataset.homeFailedDependency = section;
            document.querySelector('[data-home-opening-exit]')?.removeAttribute('hidden');
            console.warn('Required homepage preparation failed.', section, error);
            return;
        }
        if (disposed) return;
        const prepared = ['prepared', 'gsap', 'static-ready'].includes(result?.state);
        const surface = ['hero', 'vision', 'program', 'values', 'gallery'].includes(section);
        const usable = surface ? (prepared ? usableOpeningUnit(section) : installOpeningFallback(section)) : prepared;
        const state = prepared ? 'PREPARED' : 'STATIC_FALLBACK';
        ledger.settle(section, state, usable);
        root.dataset.homePreparedThrough = section;
        root.dispatchEvent(new CustomEvent('schoolai:home-preparation', {
            bubbles: true, detail: { section, status: usable ? state : 'FAILED' },
        }));
        if (!usable) {
            root.dataset.homePreparationState = 'failed';
            document.querySelector('[data-home-opening-exit]')?.removeAttribute('hidden');
            return;
        }
    }
    await progress.complete();
    if (!disposed && scrollGate.unlocked) root.dataset.homePreparationState = 'complete';
}
export function startHomepagePreparation() {
    if (disposed) return Promise.resolve();
    return preparationPromise ||= runPreparation();
}
export function scheduleHomepagePreparation() {
    if (!document.querySelector('[data-hero-slider]')) return;
    if (scheduled) return;
    scheduled = true;
    scrollGate = createHomepageScrollGate(() => preparationSignal.abort());
    const root = document.documentElement;
    root.dataset.homePreparationState = 'waiting-hero';
    let started = false;
    const start = () => {
        if (started) return;
        started = true;
        window.removeEventListener(HERO_READY_EVENT, start);
        window.setTimeout(startHomepagePreparation, 0);
    };
    window.addEventListener(HERO_READY_EVENT, start, { once: true });
    if (root.dataset.heroReady === 'true') start();
    window.addEventListener('pagehide', event => {
        if (event.persisted) return;
        disposed = true;
        preparationSignal.abort();
    });
}
