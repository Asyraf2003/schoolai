const rootElement = document.documentElement;
const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
const canEnhance = !motionQuery.matches;

if (canEnhance) rootElement.classList.add('vision-motion-capable');

let controllerPromise = null;
let scheduled = false;

function loadController() {
    if (!canEnhance) return Promise.resolve(null);
    if (controllerPromise) return controllerPromise;

    controllerPromise = import('../surfaces/home/vision-story/controller.js')
        .then(({ mountVisionStory }) => mountVisionStory())
        .catch((error) => {
            controllerPromise = null;
            rootElement.classList.remove('vision-motion-capable');
            console.error('Vision story enhancement failed.', error);
        });

    return controllerPromise;
}

function scheduleAfterHero() {
    if (scheduled || !canEnhance) return;
    scheduled = true;
    const run = () => loadController();

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(run, { timeout: 1200 });
        return;
    }

    window.setTimeout(run, 450);
}

function waitForHeroPresentation() {
    if (!document.querySelector('[data-vision-story]') || !canEnhance) return;
    const hero = document.querySelector('[data-hero-slider]');

    if (!hero || hero.getAttribute('data-enhanced') === 'true') {
        requestAnimationFrame(scheduleAfterHero);
        return;
    }

    hero.addEventListener('hero:slide-active', scheduleAfterHero, { once: true });
    window.setTimeout(scheduleAfterHero, 1100);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', waitForHeroPresentation, {
        once: true,
    });
} else {
    waitForHeroPresentation();
}
