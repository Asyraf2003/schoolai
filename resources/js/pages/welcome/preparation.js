export const HOME_PREPARATION_ORDER = Object.freeze([
  'hero',
  'program',
  'values',
  'vision',
  'gallery',
  'footer',
]);

const preparationSteps = {
  hero: () => Promise.resolve(),
  program: () => Promise.all([
    import('../../surfaces/home/program-values-world.js'),
    import('./program-cards.js'),
  ]),
  values: () => import('../../surfaces/home/values/controller.js'),
  vision: () => import('../welcome-vision-story.js')
    .then(({ prepareHomepageVisionStory }) => prepareHomepageVisionStory()),
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

async function runPreparation() {
  const failedSections = [];
  document.documentElement.dataset.homePreparationState = 'preparing';

  for (const section of HOME_PREPARATION_ORDER) {
    try {
      await preparationSteps[section]();
      reportStep(section, 'prepared');
    } catch (error) {
      failedSections.push(section);
      reportStep(section, 'failed');
      console.warn(`Homepage ${section} preparation failed.`, error);
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

  const start = () => window.setTimeout(startHomepagePreparation, 0);
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', start, { once: true });
  } else {
    start();
  }
}
