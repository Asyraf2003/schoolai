const HOMEPAGE_CURSOR_CHARACTERS = ['cwo', 'cwe'];
const CURSOR_MEDIA_BASE = 'https://media.almustaqbal.sch.id/ui/cursor';

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

const SHAKE = Object.freeze({
    minVectorPx: 4,
    minSpeedPxPerMs: 0.5,
    reversalCosine: -0.55,
    reversalWindowMs: 650,
    reversalsPerBurst: 3,
    burstRefractoryMs: 650,
    burstWindowMs: 5000,
    burstsForAnger: 3,
    dizzyMs: 3000,
    annoyedMs: 3000,
    angryMs: 3000,
    cooldownMs: 3500,
});

function closestMatch(target, selector) {
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

function preloadCursorAssets(character) {
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
    preloadCursorAssets(character);

    const cursor = document.createElement('span');
    cursor.className = 'home-cursor';
    cursor.dataset.homeCursor = '';
    cursor.dataset.state = 'default';
    cursor.setAttribute('aria-hidden', 'true');
    body.append(cursor);

    const syncCursorLayer = () => {
        const host = activeModalDialog() ?? body;

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

    let lastX = null;
    let lastY = null;
    let lastMoveAt = 0;
    let previousVector = null;
    let reversalTimes = [];
    let burstTimes = [];
    let lastBurstAt = -Infinity;

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
        reversalTimes = [];
        burstTimes = [];
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

    const registerShakeBurst = (now) => {
        if (
            emotionSequenceActive ||
            now < cooldownUntil ||
            now - lastBurstAt < SHAKE.burstRefractoryMs
        ) {
            return;
        }

        lastBurstAt = now;
        burstTimes = burstTimes.filter((time) => now - time <= SHAKE.burstWindowMs);
        burstTimes.push(now);

        if (burstTimes.length >= SHAKE.burstsForAnger) {
            startEmotionSequence();
            return;
        }

        showDizzy();
    };

    const detectShake = (event, now) => {
        if (
            baseState === 'disabled' ||
            emotionSequenceActive ||
            now < cooldownUntil
        ) {
            return;
        }

        if (lastX === null || lastY === null) {
            lastX = event.clientX;
            lastY = event.clientY;
            lastMoveAt = now;
            return;
        }

        const dx = event.clientX - lastX;
        const dy = event.clientY - lastY;
        const dt = Math.max(now - lastMoveAt, 1);
        const distance = Math.hypot(dx, dy);

        lastX = event.clientX;
        lastY = event.clientY;
        lastMoveAt = now;

        if (distance < SHAKE.minVectorPx) {
            return;
        }

        const speed = distance / dt;
        const currentVector = { dx, dy, distance };

        if (previousVector && speed >= SHAKE.minSpeedPxPerMs) {
            const dot = dx * previousVector.dx + dy * previousVector.dy;
            const cosine = dot / (distance * previousVector.distance);

            if (cosine <= SHAKE.reversalCosine) {
                reversalTimes = reversalTimes.filter(
                    (time) => now - time <= SHAKE.reversalWindowMs,
                );
                reversalTimes.push(now);

                if (reversalTimes.length >= SHAKE.reversalsPerBurst) {
                    reversalTimes = [];
                    registerShakeBurst(now);
                }
            }
        }

        previousVector = currentVector;
    };

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

        detectShake(event, now);
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
