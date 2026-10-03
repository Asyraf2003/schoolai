const CURSOR_MEDIA_BASE = 'https://media.almustaqbal.sch.id/ui/cursor';

export const INTERACTIVE_SELECTOR = [
    'a[href]',
    'button',
    '[role="button"]',
    'summary',
    'label[for]',
    'input[type="button"]',
    'input[type="submit"]',
    'input[type="reset"]',
    'input[type="range"]',
    'select',
    '.nav-mega__trigger',
    '.nav-language__button',
    '.nav-language__option',
    '.nav-hero-audio__text',
    '.navbar__hero-audio-icon',
    '.nilai-card',
    '.program-card',
    '.program-kinetic__trigger',
    '.galeri-story-card',
    '.galeri-story-visual__panel.is-active',
    '.galeri-story-visual__media',
    '.galeri-story-visual__play',
    '.galeri-story-card__mobile-media',
    '.galeri-story-card__mobile-play',
    '.gallery-wall-card',
    '.faq-card summary',
].join(',');

export const DISABLED_SELECTOR = [
    '[disabled]',
    '[aria-disabled="true"]',
    '.is-disabled',
    '.footer-channel--disabled',
].join(',');

export function closestMatch(target, selector) {
    return target instanceof Element ? target.closest(selector) : null;
}

function activeModalDialog() {
    const openDialogs = Array.from(document.querySelectorAll('dialog[open]'));

    for (let index = openDialogs.length - 1; index >= 0; index -= 1) {
        const dialog = openDialogs[index];

        try {
            if (dialog.matches(':modal')) {
                return dialog;
            }
        } catch {
            return dialog;
        }
    }

    return null;
}

export function activeCursorLayerHost() {
    const fullscreenElement = document.fullscreenElement ?? document.webkitFullscreenElement;

    if (fullscreenElement instanceof Element) {
        return fullscreenElement;
    }

    return activeModalDialog();
}

export function loadCursorAsset(character, suffix, onReady) {
    const image = new Image();
    image.onload = () => {
        const decoded = typeof image.decode === 'function' ? image.decode() : Promise.resolve();
        decoded.then(() => onReady(true), () => onReady(false));
    };
    image.onerror = () => onReady(false);
    image.src = `${CURSOR_MEDIA_BASE}/${character}${suffix}.webp`;
    return () => {
        image.onload = null;
        image.onerror = null;
        image.removeAttribute?.('src');
    };
}
