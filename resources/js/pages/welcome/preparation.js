import { createHomepageScrollGate } from './scroll-gate.js';

export const HOME_PREPARATION_ORDER = Object.freeze([
  'hero',
  'vision',
  'program',
  'values',
  'gallery',
  'footer',
]);

const HERO_READY_EVENT = 'schoolai:hero-ready';

const firstJourney = new AbortController();
let scrollGate = null;

const preparationSteps = {
  hero: () => Promise.resolve(),
  vision: () => import('../welcome-vision-story.js')
    .then(({ prepareHomepageVisionStory }) => prepareHomepageVisionStory({ signal: firstJourney.signal })),
  program: () => Promise.all([
    import('../../surfaces/home/program-values-world.js'),
    import('./program-cards.js')
      .then(({ prepareHomepageProgram }) => prepareHomepageProgram()),
  ]),
  values: () => import('../../surfaces/home/values/controller.js'),
  gallery: () => import('../welcome-depth-gallery.js')
    .then(({ prepareHomepageDepthGallery }) => prepareHomepageDepthGallery()),
  footer: () => Promise.resolve(),
};

let preparationPromise = null;
let scheduled = false;

function reportStep(section, status) {
  const root = document.documentElement;
  root.dataset.homePreparedThrough = section;
  root.dispatchEvent(new CustomEvent('schoolai:home-preparation', {
    bubbles: true,
    detail: { section, status },
  }));
}

function yieldToBrowser() {
  return new Promise((resolve) => window.setTimeout(resolve, 0));
}

async function runPreparation() {
  const failedSections = [];
  document.documentElement.dataset.homePreparationState = 'preparing';

  for (const section of HOME_PREPARATION_ORDER) {
    try {
      const work = preparationSteps[section]();
      const result = section === 'vision'
        ? await Promise.race([work, scrollGate?.fallbackReady || new Promise(() => {})])
        : await work;
      if (section === 'vision') scrollGate?.release(result?.state || 'prepared');
      reportStep(section, result?.state || 'prepared');
    } catch (error) {
      if (section === 'vision') { firstJourney.abort(); scrollGate?.release('static-fallback'); }
      failedSections.push(section);
      reportStep(section, 'failed');
      console.warn(`Homepage ${section} preparation failed.`, error);
    }

    if (section !== HOME_PREPARATION_ORDER.at(-1)) {
      await yieldToBrowser();
    }
  }

  const root = document.documentElement;
  root.dataset.homePreparationState = 'complete';
  if (failedSections.length) {
    root.dataset.homePreparationFailures = failedSections.join(',');
  }
}

export function startHomepagePreparation() {
  if (!document.querySelector('[data-program-values-world]')) {
    return Promise.resolve();
  }
  if (!preparationPromise) preparationPromise = runPreparation();
  return preparationPromise;
}

export function scheduleHomepagePreparation() {
  if (scheduled) return;
  scheduled = true;
  scrollGate = createHomepageScrollGate(() => firstJourney.abort());

  const root = document.documentElement;
  root.dataset.homePreparationState = 'waiting-hero';

  const arm = () => {
    let started = false;
    const start = () => {
      if (started) return;
      started = true;
      window.removeEventListener(HERO_READY_EVENT, start);
      window.setTimeout(startHomepagePreparation, 0);
    };

    window.addEventListener(HERO_READY_EVENT, start, { once: true });

    if (
      root.dataset.heroReady === 'true'
      || !document.querySelector('[data-hero-slider]')
    ) {
      start();
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', arm, { once: true });
  } else {
    arm();
  }
}
