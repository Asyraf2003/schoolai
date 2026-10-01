import { createHomepageScrollGate } from './scroll-gate.js';
import { createOpeningLedger, REQUIRED_OPENING_UNITS } from './opening-ledger.js';
import { createOpeningProgress } from './opening-progress.js';
import { installOpeningFallback, prepareHeroActiveMedia, usableOpeningUnit } from './opening-readiness.js';

export const HOME_PREPARATION_ORDER = REQUIRED_OPENING_UNITS;
const HERO_READY_EVENT = 'schoolai:hero-ready';
const preparationSignal = new AbortController();
let scrollGate = null;
let preparationPromise = null;
let scheduled = false;
let disposed = false;
const preparationSteps = {
    hero: () => prepareHeroActiveMedia({ signal: preparationSignal.signal }),
    vision: () => import('../welcome-vision-story.js')
        .then(({ prepareHomepageVisionStory }) => preparationSignal.signal.aborted ? { state: 'static-fallback' } : prepareHomepageVisionStory({ signal: preparationSignal.signal })),
    program: () => Promise.all([
        import('../../surfaces/home/program-values-world.js').then(({ mountProgramValuesWorld }) => {
            mountProgramValuesWorld(document.querySelector('[data-program-values-world]'), { signal: preparationSignal.signal });
        }),
        import('./program-cards.js').then(({ prepareHomepageProgram }) => preparationSignal.signal.aborted ? { state: 'static-fallback' } : prepareHomepageProgram({ signal: preparationSignal.signal })),
    ]).then(([, result]) => result),
    values: () => import('../../surfaces/home/values/preparation.js')
        .then(({ prepareHomepageValues }) => preparationSignal.signal.aborted ? { state: 'static-fallback' } : prepareHomepageValues({ signal: preparationSignal.signal })),
    gallery: () => import('../welcome-depth-gallery.js')
        .then(({ prepareHomepageDepthGallery }) => preparationSignal.signal.aborted ? { state: 'static-fallback' } : prepareHomepageDepthGallery({ signal: preparationSignal.signal })),
};
function yieldToBrowser() {
    if (window.scheduler?.yield) return window.scheduler.yield();
    return new Promise(resolve => window.setTimeout(resolve, 0));
}
function aborted() {
    return new Promise(resolve => {
        if (preparationSignal.signal.aborted) resolve({ state: 'static-fallback' });
        else preparationSignal.signal.addEventListener('abort', () => resolve({ state: 'static-fallback' }), { once: true });
    });
}
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
            // A decoded first frame is sufficient; no video completion is awaited.
            result = await Promise.race([preparationSteps[section](), aborted()]);
        } catch {
            result = { state: 'failed' };
        }
        if (disposed) return;
        const prepared = ['prepared', 'gsap'].includes(result?.state);
        const usable = prepared ? usableOpeningUnit(section) : installOpeningFallback(section);
        const state = prepared ? 'PREPARED' : 'STATIC_FALLBACK';
        ledger.settle(section, state, usable);
        root.dataset.homePreparedThrough = section;
        root.dispatchEvent(new CustomEvent('schoolai:home-preparation', {
            bubbles: true, detail: { section, status: usable ? state : 'FAILED' },
        }));
    }
    if (!ledger.complete) {
        root.dataset.homePreparationState = 'failed';
        document.querySelector('[data-home-opening-exit]')?.removeAttribute('hidden');
        return;
    }
    await progress.complete();
    if (!disposed && scrollGate.unlocked) root.dataset.homePreparationState = 'complete';
}
export function startHomepagePreparation() {
    if (disposed) return Promise.resolve();
    return preparationPromise ||= runPreparation();
}
export function scheduleHomepagePreparation() {
    if (scheduled) return;
    scheduled = true;
    scrollGate = createHomepageScrollGate(() => { preparationSignal.abort(); startHomepagePreparation(); });
    const root = document.documentElement;
    if (scrollGate.unlocked || root.dataset.homeFallbackReason === 'runtime-unavailable') preparationSignal.abort();
    root.dataset.homePreparationState = 'waiting-hero';
    let started = false;
    const start = () => {
        if (started) return;
        started = true;
        window.removeEventListener(HERO_READY_EVENT, start);
        window.setTimeout(startHomepagePreparation, 0);
    };
    window.addEventListener(HERO_READY_EVENT, start, { once: true });
    if (root.dataset.heroReady === 'true' || preparationSignal.signal.aborted) start();
    window.addEventListener('pagehide', event => {
        if (event.persisted) return;
        disposed = true;
        preparationSignal.abort();
    });
}
