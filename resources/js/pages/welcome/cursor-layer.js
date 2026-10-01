const HOMEPAGE_CURSOR_CHARACTERS = ['cwo', 'cwe'];
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

export function preloadCursorAssets(character) {
    ['1', '2', '3', '4', '5'].forEach((suffix) => {
        const href = `${CURSOR_MEDIA_BASE}/${character}${suffix}.webp`;

        if (!document.head.querySelector(`link[rel="preload"][href="${href}"]`)) {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.type = 'image/webp';
            link.href = href;
            document.head.append(link);
        }

        const image = new Image();
        image.src = href;
    });
}

