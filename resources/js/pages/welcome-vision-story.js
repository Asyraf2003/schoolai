const rootElement = document.documentElement;
let controllerPromise = null;

export function prepareHomepageVisionStory({ signal } = {}) {
    const root = document.querySelector('[data-vision-story]');
    if (!root) return Promise.resolve({ state: 'absent' });
    if (signal?.aborted) {
        root.dataset.visionState = 'static-fallback';
        return Promise.resolve({ state: 'static-fallback' });
    }
    if (controllerPromise) return controllerPromise;
    const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    rootElement.classList.toggle('vision-motion-capable', !motionQuery.matches);
    controllerPromise = import('../surfaces/home/vision-story/controller.js')
        .then(({ mountVisionStory }) => {
            if (signal?.aborted) return { state: 'static-fallback' };
            return mountVisionStory({ signal }).ready;
        }).catch(error => {
            rootElement.classList.remove('vision-motion-capable');
            root.dataset.visionState = 'static-fallback';
            console.warn('Vision preparation uses semantic fallback.', error);
            return { state: 'static-fallback' };
        });
    return controllerPromise;
}
