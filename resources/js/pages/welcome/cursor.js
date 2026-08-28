const HOMEPAGE_CURSOR_CHARACTERS = ['cwo', 'cwe'];

const INTERACTIVE_SELECTOR = [
    'a[href]',
    'button',
    '[role="button"]',
    'summary',
    'label[for]',
    'input[type="button"]',
    'input[type="submit"]',
    'input[type="reset"]',
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

const DISABLED_SELECTOR = [
    '[disabled]',
    '[aria-disabled="true"]',
    '.is-disabled',
    '.footer-channel--disabled',
].join(',');

function closestMatch(target, selector) {
    return target instanceof Element ? target.closest(selector) : null;
}

export function initHomepageCursor() {
    const body = document.body;

    if (!body?.classList.contains('home-page')) {
        return;
    }

    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        body.removeAttribute('data-cursor-character');
        return;
    }

    const character =
        HOMEPAGE_CURSOR_CHARACTERS[
            Math.floor(Math.random() * HOMEPAGE_CURSOR_CHARACTERS.length)
        ];

    body.dataset.cursorCharacter = character;

    const cursor = document.createElement('span');
    cursor.className = 'home-cursor';
    cursor.dataset.homeCursor = '';
    cursor.dataset.state = 'default';
    cursor.setAttribute('aria-hidden', 'true');
    body.append(cursor);

    let pointerX = -200;
    let pointerY = -200;
    let frameId = 0;
    let state = 'default';

    const paint = () => {
        frameId = 0;
        cursor.style.setProperty('--home-cursor-x', `${pointerX}px`);
        cursor.style.setProperty('--home-cursor-y', `${pointerY}px`);
    };

    const schedulePaint = () => {
        if (!frameId) {
            frameId = window.requestAnimationFrame(paint);
        }
    };

    const setState = (nextState) => {
        if (state === nextState) {
            return;
        }

        state = nextState;
        cursor.dataset.state = nextState;
    };

    const handlePointerMove = (event) => {
        pointerX = event.clientX;
        pointerY = event.clientY;

        const disabled = closestMatch(event.target, DISABLED_SELECTOR);
        if (disabled) {
            setState('disabled');
            cursor.classList.remove('is-visible');
            schedulePaint();
            return;
        }

        const interactive = closestMatch(event.target, INTERACTIVE_SELECTOR);
        setState(interactive ? 'interactive' : 'default');
        cursor.classList.add('is-visible');
        schedulePaint();
    };

    const hideCursor = () => {
        cursor.classList.remove('is-visible');
    };

    document.addEventListener('pointermove', handlePointerMove, { passive: true });
    document.documentElement.addEventListener('pointerleave', hideCursor, { passive: true });
    window.addEventListener('blur', hideCursor, { passive: true });
}
