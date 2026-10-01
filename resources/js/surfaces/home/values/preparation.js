import { createValuesStory } from './controller.js';
let preparation = null;
export function prepareHomepageValues({ signal } = {}) {
    if (preparation) return preparation;
    const root = document.querySelector('[data-values-story]');
    const cleanup = root ? createValuesStory(root) : null;
    signal?.addEventListener('abort', () => cleanup?.(), { once: true });
    if (signal?.aborted) cleanup?.();
    return preparation = cleanup?.ready || Promise.resolve({ state: 'absent' });
}
