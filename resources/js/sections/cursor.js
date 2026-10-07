const ACTIONABLE = 'a[href], button, [role="button"], summary, input[type="submit"], input[type="button"], select, [data-actionable]';
const BASE = 'https://media.almustaqbal.sch.id/ui/cursor';

export function chooseCharacter(random = Math.random) {
    return random() < 0.5 ? 'cwo' : 'cwe';
}

export function cursorState(target) {
    const control = target?.closest?.(ACTIONABLE);
    return control && !control.matches(':disabled, [aria-disabled="true"]')
        && !control.closest('[inert]') ? 'interactive' : 'default';
}

export function mountCursor() {
    const character = chooseCharacter();
    const capability = window.matchMedia('(hover: hover) and (pointer: fine)');
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal, passive: true };
    let cursor;
    let loading;
    let ready = false;
    let suspended = document.hidden;
    let point;
    let frame = 0;
    const images = [];
    function hide() {
        cancelAnimationFrame(frame);
        frame = 0;
        point = null;
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
        host();
        cursor.dataset.state = cursorState(point.target);
        cursor.style.setProperty('--cursor-x', `${point.x}px`);
        cursor.style.setProperty('--cursor-y', `${point.y}px`);
        cursor.dataset.visible = '';
        document.body.dataset.customCursor = '';
    }
    function move(event) {
        if (event.pointerType === 'touch' || !capability.matches) { hide(); return; }
        point = { x: event.clientX, y: event.clientY, target: event.target };
        if (ready && !frame && !suspended) frame = requestAnimationFrame(paint);
    }
    function activate() {
        hide();
        if (!capability.matches || loading) return;
        cursor = document.createElement('span');
        cursor.className = 'site-cursor';
        cursor.dataset.character = character;
        cursor.dataset.state = 'default';
        cursor.setAttribute('aria-hidden', 'true');
        host();
        loading = Promise.all([1, 2].map(suffix => new Promise(resolve => {
            const image = new Image();
            images.push(image);
            image.onload = () => resolve(true);
            image.onerror = () => resolve(false);
            image.src = `${BASE}/${character}${suffix}.webp`;
        }))).then(loaded => {
            ready = loaded.every(Boolean);
            if (ready && point && !suspended && !frame && !lifecycle.signal.aborted) frame = requestAnimationFrame(paint);
        });
    }
    const observer = new MutationObserver(host);
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
            images.forEach(image => { image.onload = image.onerror = null; });
            cursor?.remove();
        },
    };
}
