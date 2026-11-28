const BASE = 'https://media.almustaqbal.sch.id/ui/cursor';
const ASSETS = {
    cwo: { default: `${BASE}/cwo1.webp`, pointer: `${BASE}/cwo.webp` },
    cwe: { default: `${BASE}/cwe.webp`, pointer: `${BASE}/cwe1.webp` },
};
const POINTER = ':any-link, button, .site-header summary, .program__trigger';
const BLOCKED = ':disabled, [aria-disabled="true"], [inert]';
const INERT_CURSOR = new Set(['', 'inherit', 'unset', 'revert', 'revert-layer']);

export function chooseCharacter(random = Math.random) {
    return random() < 0.5 ? 'cwo' : 'cwe';
}

function inlineCursor(element) {
    for (let node = element; node && node.nodeType === 1; node = node.parentElement) {
        if (node.matches?.(BLOCKED)) return 'default';
        const inline = node.style?.cursor;
        if (inline && !INERT_CURSOR.has(inline)) return inline === 'pointer' ? 'pointer' : 'default';
        if (node.matches?.(POINTER)) return 'pointer';
    }
    return '';
}

export function cursorState(target) {
    const element = target && target.nodeType === 3 ? target.parentElement : target;
    if (!element || typeof element.closest !== 'function') return 'default';
    if (element.closest(BLOCKED)) return 'default';
    const override = element.closest('[style*="cursor"]') ? inlineCursor(element) : '';
    if (override) return override;
    if (typeof getComputedStyle === 'function' && typeof Element === 'function' && element instanceof Element) {
        const intent = getComputedStyle(element).getPropertyValue('--cursor-intent').trim();
        if (intent === 'pointer' || intent === 'default') return intent;
    }
    const control = element.closest(POINTER);
    return control && !control.matches?.(BLOCKED) ? 'pointer' : 'default';
}

export function mountCursor() {
    const character = chooseCharacter();
    const sources = ASSETS[character];
    const capability = window.matchMedia('(hover: hover) and (pointer: fine)');
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal, passive: true };
    let cursor;
    let preload;
    let state = 'default';
    let ready = false;
    let failed = false;
    let suspended = document.hidden;
    let point;
    let frame = 0;
    let visible = false;
    function hide() {
        cancelAnimationFrame(frame);
        frame = 0;
        point = null;
        if (!visible) return;
        visible = false;
        cursor?.removeAttribute('data-visible');
        document.body.removeAttribute('data-custom-cursor');
    }
    function host() {
        if (!cursor) return;
        const modal = [...document.querySelectorAll('dialog[open]')].findLast(dialog => dialog.matches(':modal'));
        const parent = document.fullscreenElement ?? modal ?? document.body;
        if (cursor.parentElement !== parent) parent.append(cursor);
    }
    function paint() {
        frame = 0;
        if (!ready || suspended || !capability.matches || !point) return;
        const nextState = cursorState(point.target);
        if (nextState !== state) {
            state = nextState;
            cursor.dataset.state = state;
            cursor.src = sources[state];
        }
        cursor.style.setProperty('--cursor-x', `${point.x}px`);
        cursor.style.setProperty('--cursor-y', `${point.y}px`);
        if (visible) return;
        visible = true;
        cursor.setAttribute('data-visible', '');
        document.body.setAttribute('data-custom-cursor', '');
    }
    function move(event) {
        if (event.pointerType === 'touch' || !capability.matches) { hide(); return; }
        const target = event.target?.nodeType === 3 ? event.target.parentElement : event.target ?? null;
        if (point?.x === event.clientX && point?.y === event.clientY && point?.target === target) return;
        point = { x: event.clientX, y: event.clientY, target };
        if (ready && !frame && !suspended) frame = requestAnimationFrame(paint);
    }
    function activate() {
        hide();
        if (!capability.matches || cursor || failed) return;
        cursor = document.createElement('img');
        cursor.className = 'site-cursor';
        cursor.dataset.character = character;
        cursor.dataset.state = state;
        cursor.alt = '';
        cursor.draggable = false;
        cursor.setAttribute('aria-hidden', 'true');
        let defaultLoaded = false;
        let pointerLoaded = false;
        const finishLoad = () => {
            if (failed || lifecycle.signal.aborted) return;
            ready = defaultLoaded && pointerLoaded;
            if (ready && point && !suspended && !frame) frame = requestAnimationFrame(paint);
        };
        const failLoad = () => {
            failed = true;
            ready = false;
            hide();
        };
        cursor.addEventListener('error', failLoad, options);
        cursor.addEventListener('load', () => {
            if (cursor.naturalWidth <= 0) { failLoad(); return; }
            defaultLoaded = true;
            finishLoad();
        }, { ...options, once: true });
        preload = new Image();
        preload.onload = () => {
            if (preload.naturalWidth <= 0) { failLoad(); return; }
            pointerLoaded = true;
            finishLoad();
        };
        preload.onerror = failLoad;
        preload.src = sources.pointer;
        cursor.src = sources.default;
        host();
    }
    const observer = new MutationObserver(changes => {
        if (changes.some(change => change.target.matches('dialog'))) host();
    });
    observer.observe(document.body, { subtree: true, attributes: true, attributeFilter: ['open'] });
    document.addEventListener('pointermove', move, options);
    document.addEventListener('pointerover', move, options);
    document.documentElement.addEventListener('pointerleave', hide, options);
    document.addEventListener('fullscreenchange', host, options);
    document.addEventListener('visibilitychange', () => { suspended = document.hidden; hide(); }, options);
    window.addEventListener('blur', hide, options);
    capability.addEventListener('change', activate, options);
    window.addEventListener('pagehide', () => { suspended = true; hide(); }, options);
    window.addEventListener('pageshow', () => { suspended = document.hidden; }, options);
    activate();
    return {
        dispose() {
            hide();
            lifecycle.abort();
            observer.disconnect();
            if (preload) { preload.onload = preload.onerror = null; preload = null; }
            cursor?.remove();
            cursor = null;
        },
    };
}
