import { INTERACTIVE_SELECTOR, DISABLED_SELECTOR, closestMatch, activeCursorLayerHost, preloadCursorAssets } from './cursor-layer.js';
import { SHAKE, createCursorGesture } from './cursor-gesture.js';
const HOMEPAGE_CURSOR_CHARACTERS = ['cwo', 'cwe'];

export function initHomepageCursor() {
    const body = document.body;

    if (!body?.classList.contains('site-cursor-page')) {
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
    preloadCursorAssets(character);

    const cursor = document.createElement('span');
    cursor.className = 'home-cursor';
    cursor.dataset.homeCursor = '';
    cursor.dataset.viewportOverlay = '';
    cursor.dataset.state = 'default';
    cursor.setAttribute('aria-hidden', 'true');
    body.append(cursor);

    const syncCursorLayer = () => {
        const host = activeCursorLayerHost() ?? body;

        if (cursor.parentElement !== host) {
            host.append(cursor);
        }
    };

    const modalLayerObserver = new MutationObserver(syncCursorLayer);
    modalLayerObserver.observe(body, {
        subtree: true,
        attributes: true,
        attributeFilter: ['open'],
    });

    document.addEventListener('fullscreenchange', syncCursorLayer, { passive: true });
    document.addEventListener('webkitfullscreenchange', syncCursorLayer, { passive: true });
    syncCursorLayer();

    let pointerX = -200;
    let pointerY = -200;
    let frameId = 0;
    let renderedState = 'default';
    let baseState = 'default';
    let emotionState = null;
    let emotionTimer = 0;
    let emotionSequenceActive = false;
    let cooldownUntil = 0;

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

    const setRenderedState = (nextState) => {
        if (renderedState === nextState) {
            return;
        }

        renderedState = nextState;
        cursor.dataset.state = nextState;
    };

    const syncVisualState = () => {
        if (baseState === 'disabled') {
            setRenderedState('disabled');
            cursor.classList.remove('is-visible');
            return;
        }

        setRenderedState(emotionState ?? baseState);
        cursor.classList.add('is-visible');
    };

    const clearEmotionTimer = () => {
        if (!emotionTimer) {
            return;
        }

        window.clearTimeout(emotionTimer);
        emotionTimer = 0;
    };

    const showDizzy = () => {
        if (emotionSequenceActive) {
            return;
        }

        clearEmotionTimer();
        emotionState = 'dizzy';
        syncVisualState();

        emotionTimer = window.setTimeout(() => {
            emotionTimer = 0;
            emotionState = null;
            syncVisualState();
        }, SHAKE.dizzyMs);
    };

    const startEmotionSequence = () => {
        clearEmotionTimer();
        gestures.reset();
        emotionSequenceActive = true;
        emotionState = 'dizzy';
        syncVisualState();

        emotionTimer = window.setTimeout(() => {
            emotionState = 'annoyed';
            syncVisualState();

            emotionTimer = window.setTimeout(() => {
                emotionState = 'angry';
                syncVisualState();

                emotionTimer = window.setTimeout(() => {
                    emotionTimer = 0;
                    emotionState = null;
                    emotionSequenceActive = false;
                    cooldownUntil = performance.now() + SHAKE.cooldownMs;
                    syncVisualState();
                }, SHAKE.angryMs);
            }, SHAKE.annoyedMs);
        }, SHAKE.dizzyMs);
    };

    const gestures = createCursorGesture(
        () => ({ baseState, emotionSequenceActive, cooldownUntil }),
        showDizzy, startEmotionSequence,
    );

    const handlePointerMove = (event) => {
        const now = performance.now();
        pointerX = event.clientX;
        pointerY = event.clientY;

        const disabled = closestMatch(event.target, DISABLED_SELECTOR);
        if (disabled) {
            baseState = 'disabled';
        } else {
            const interactive = closestMatch(event.target, INTERACTIVE_SELECTOR);
            baseState = interactive ? 'interactive' : 'default';
        }

        gestures.detectShake(event, now);
        syncVisualState();
        schedulePaint();
    };

    const hideCursor = () => {
        cursor.classList.remove('is-visible');
    };

    document.addEventListener('pointermove', handlePointerMove, { passive: true });
    document.documentElement.addEventListener('pointerleave', hideCursor, { passive: true });
    window.addEventListener('blur', hideCursor, { passive: true });
}
