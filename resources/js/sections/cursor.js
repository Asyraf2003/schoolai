const BASE = 'https://media.almustaqbal.sch.id/ui/cursor';

export function chooseCharacter(random = Math.random) {
    return random() < 0.5 ? 'cwo' : 'cwe';
}

export function mountCursor() {
    const character = chooseCharacter();
    const capability = window.matchMedia('(hover: hover) and (pointer: fine)');
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal, passive: true };
    let cursor;
    let ready = false;
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
        cursor.removeAttribute('data-visible');
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
        cursor.style.setProperty('--cursor-x', `${point.x}px`);
        cursor.style.setProperty('--cursor-y', `${point.y}px`);
        if (visible) return;
        visible = true;
        cursor.setAttribute('data-visible', '');
        document.body.setAttribute('data-custom-cursor', '');
    }
    function move(event) {
        if (event.pointerType === 'touch' || !capability.matches) { hide(); return; }
        if (point?.x === event.clientX && point?.y === event.clientY) return;
        point = { x: event.clientX, y: event.clientY };
        if (ready && !frame && !suspended) frame = requestAnimationFrame(paint);
    }
    function activate() {
        hide();
        if (!capability.matches || cursor) return;
        cursor = document.createElement('img');
        cursor.className = 'site-cursor';
        cursor.dataset.character = character;
        cursor.alt = '';
        cursor.draggable = false;
        cursor.setAttribute('aria-hidden', 'true');
        cursor.addEventListener('load', () => {
            ready = cursor.naturalWidth > 0;
            if (ready && point && !suspended && !frame) frame = requestAnimationFrame(paint);
        }, { ...options, once: true });
        cursor.src = `${BASE}/${character}1.webp`;
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
            cursor?.remove();
        },
    };
}
