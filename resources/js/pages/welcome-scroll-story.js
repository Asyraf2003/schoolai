import { createStoryController } from './welcome-scroll-story/controller.js';

let destroyStories = null;

function initialiseStories() {
    if (typeof destroyStories === 'function') destroyStories();

    document.documentElement.classList.add('has-scroll-story');

    const cleanups = Array.from(
        document.querySelectorAll('[data-story-root]')
    ).map(createStoryController);

    destroyStories = function destroyAllStories() {
        cleanups.forEach((cleanup) => cleanup());
        document.documentElement.classList.remove('has-scroll-story');
        destroyStories = null;
    };
}

function onReady() {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialiseStories, { once: true });
        return;
    }

    initialiseStories();
}

onReady();

window.addEventListener('pagehide', () => {
    if (typeof destroyStories === 'function') destroyStories();
});

window.addEventListener('pageshow', (event) => {
    if (event.persisted) initialiseStories();
});
