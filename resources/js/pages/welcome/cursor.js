import { INTERACTIVE_SELECTOR, DISABLED_SELECTOR, closestMatch, activeCursorLayerHost, loadCursorAsset } from './cursor-layer.js';

export function initHomepageCursor({ prepareAll = false } = {}) {
    const body = document.body;
    const capability = window.matchMedia('(hover: hover) and (pointer: fine)');
    if (!body?.classList.contains('site-cursor-page') || !capability.matches) return;

    const character = ['cwo', 'cwe'][Math.floor(Math.random() * 2)];
    const cursor = document.createElement('span');
    cursor.className = 'home-cursor';
    cursor.dataset.homeCursor = '';
    cursor.dataset.viewportOverlay = '';
    cursor.dataset.state = 'default';
    cursor.setAttribute('aria-hidden', 'true');
    body.append(cursor);

    const lifecycle = new AbortController();
    const options = { passive: true, signal: lifecycle.signal };
    let disposed = false;
    let suspended = document.hidden;
    let pointerX = null;
    let pointerY = null;
    let frameId = 0;
    let state = 'default';
    let ready = false;
    let hoverReady = false;
    let cancelHover = null;

    const syncCursorLayer = () => {
        const host = activeCursorLayerHost() ?? body;
        if (cursor.parentElement !== host) host.append(cursor);
    };

    const hide = () => {
        window.cancelAnimationFrame(frameId);
        frameId = 0;
        pointerX = null;
        body.removeAttribute('data-cursor-character');
        cursor.classList.remove('is-visible');
    };

    const paint = () => {
        frameId = 0;
        if (!ready || disposed || suspended || !capability.matches || pointerX === null) return;
        cursor.style.setProperty('--home-cursor-x', `${pointerX}px`);
        cursor.style.setProperty('--home-cursor-y', `${pointerY}px`);
        cursor.dataset.state = state === 'interactive' && !hoverReady ? 'default' : state;
        body.dataset.cursorCharacter = character;
        cursor.classList.toggle('is-visible', state !== 'disabled');
    };

    const schedulePaint = () => {
        if (ready && capability.matches && pointerX !== null && !frameId && !disposed && !suspended) {
            frameId = window.requestAnimationFrame(paint);
        }
    };

    let defaultDone, hoverDone;
    const defaultReady = new Promise(resolve => { defaultDone = resolve; });
    const hoverAssetReady = new Promise(resolve => { hoverDone = resolve; });
    const cancelDefault = loadCursorAsset(character, '1', (loaded) => {
        if (disposed) return;
        defaultDone();
        ready = loaded;
        if (ready) schedulePaint();
        else hide();
    });

    const handlePointerMove = (event) => {
        if (event.pointerType === 'touch') { hide(); return; }
        pointerX = event.clientX;
        pointerY = event.clientY;
        schedulePaint();
    };

    function warmHover() {
        if (cancelHover) return;
        cancelHover = loadCursorAsset(character, '2', loaded => {
            hoverDone();
            if (disposed) return;
            hoverReady = loaded;
            schedulePaint();
        });
    }
    if (prepareAll) warmHover();
    const handlePointerOver = (event) => {
        state = closestMatch(event.target, DISABLED_SELECTOR) ? 'disabled'
            : closestMatch(event.target, INTERACTIVE_SELECTOR) ? 'interactive' : 'default';
        if (state === 'interactive') warmHover();
        schedulePaint();
    };

    const modalLayerObserver = new MutationObserver(syncCursorLayer);
    modalLayerObserver.observe(body, { subtree: true, attributes: true, attributeFilter: ['open'] });
    document.addEventListener('fullscreenchange', syncCursorLayer, options);
    document.addEventListener('webkitfullscreenchange', syncCursorLayer, options);
    document.addEventListener('pointermove', handlePointerMove, options);
    document.addEventListener('pointerover', handlePointerOver, options);
    document.documentElement.addEventListener('pointerleave', hide, options);
    window.addEventListener('blur', hide, options);
    document.addEventListener('visibilitychange', () => {
        suspended = document.hidden;
        if (suspended) hide();
    }, options);
    capability.addEventListener('change', hide, options);

    const destroy = () => {
        disposed = true;
        hide();
        lifecycle.abort();
        modalLayerObserver.disconnect();
        cancelDefault();
        cancelHover?.();
        cursor.remove();
    };
    window.addEventListener('pagehide', (event) => {
        if (!event.persisted) destroy();
        else { suspended = true; hide(); }
    }, options);
    window.addEventListener('pageshow', () => {
        suspended = document.hidden;
        syncCursorLayer();
    }, options);
    syncCursorLayer();
    destroy.ready = prepareAll ? Promise.all([defaultReady, hoverAssetReady]) : defaultReady;
    return destroy;
}
