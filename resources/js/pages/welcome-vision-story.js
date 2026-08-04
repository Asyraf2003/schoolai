let controllerPromise = null;
let scheduled = false;

function loadController() {
    if (controllerPromise) return controllerPromise;

    controllerPromise = import('../surfaces/home/vision-story/controller.js')
        .then(({ mountVisionStory }) => mountVisionStory())
        .catch((error) => {
            controllerPromise = null;
            console.error('Vision story enhancement failed.', error);
        });

    return controllerPromise;
}

function scheduleAfterHero() {
    if (scheduled) return;
    scheduled = true;

    const run = () => loadController();

    if ('requestIdleCallback' in window) {
        window.requestIdleCallback(run, { timeout: 1600 });
        return;
    }

    window.setTimeout(run, 650);
}

function waitForHeroPresentation() {
    if (!document.querySelector('[data-vision-story]')) return;

    const hero = document.querySelector('[data-hero-slider]');

    if (!hero || hero.getAttribute('data-enhanced') === 'true') {
        requestAnimationFrame(() => requestAnimationFrame(scheduleAfterHero));
        return;
    }

    hero.addEventListener('hero:slide-active', scheduleAfterHero, { once: true });
    window.setTimeout(scheduleAfterHero, 1400);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', waitForHeroPresentation, { once: true });
} else {
    waitForHeroPresentation();
}
