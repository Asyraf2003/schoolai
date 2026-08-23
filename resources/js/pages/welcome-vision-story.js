const rootElement = document.documentElement;
const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
const canEnhance = !motionQuery.matches;

if (canEnhance) rootElement.classList.add('vision-motion-capable');

let controllerPromise = null;

export function prepareHomepageVisionStory() {
    if (!document.querySelector('[data-vision-story]') || !canEnhance) {
        return Promise.resolve(null);
    }
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
