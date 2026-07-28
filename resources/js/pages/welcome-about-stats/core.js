export var ROOT_SELECTOR = '[data-about-reel]';
export var TRACK_SELECTOR = '[data-about-reel-track]';
export var DESKTOP_QUERY = '(min-width: 1024px) and (pointer: fine)';
export var REDUCED_MOTION_QUERY = '(prefers-reduced-motion: reduce)';

export function clamp(value, minimum, maximum) {
    return Math.min(Math.max(value, minimum), maximum);
}

export function addMediaListener(mediaQuery, listener) {
    if (typeof mediaQuery.addEventListener === 'function') {
        mediaQuery.addEventListener('change', listener);
        return;
    }

    mediaQuery.addListener(listener);
}

export function removeMediaListener(mediaQuery, listener) {
    if (typeof mediaQuery.removeEventListener === 'function') {
        mediaQuery.removeEventListener('change', listener);
        return;
    }

    mediaQuery.removeListener(listener);
}

export function onDocumentReady(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback, { once: true });
        return;
    }

    callback();
}
