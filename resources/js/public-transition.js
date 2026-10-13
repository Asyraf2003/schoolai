const key = 'schoolai-public-transition';
const root = document.documentElement;
let navigating = false;
let deadline;

export function isPublicDestination(href, current = location.href) {
    const url = new URL(href, current), source = new URL(current);
    return url.origin === source.origin && /^\/(?:galeri\/?|artikel(?:\/[^/]+)?\/?)?$/.test(url.pathname)
        && !(url.pathname === source.pathname && url.search === source.search);
}

export function navigatePublic(href) {
    if (navigating) return;
    const url = new URL(href, location.href);
    const layer = document.querySelector('[data-public-transition]');
    if (!layer || !isPublicDestination(url.href) || document.hidden
        || matchMedia('(prefers-reduced-motion: reduce)').matches) {
        location.assign(url.href);
        return;
    }
    let palette = Math.floor(Math.random() * 4);
    if (String(palette) === root.dataset.transitionPalette) palette = (palette + 1) % 4;
    try {
        sessionStorage.setItem(key, JSON.stringify({ url: url.href, at: Date.now(), palette }));
    } catch { location.assign(url.href); return; }
    navigating = true;
    root.dataset.transitionPalette = String(palette);
    root.dataset.publicTransition = 'exit';
    document.dispatchEvent(new Event('public:navigate'));
    let assigned = false;
    const go = () => {
        if (assigned) return;
        assigned = true;
        clearTimeout(deadline);
        location.assign(url.href);
    };
    const last = layer.querySelector('.public-transition__slice:nth-child(8)');
    last.addEventListener('animationend', go, { once: true });
    // Bounded fallback for disabled CSS, interrupted animations or missing events.
    deadline = setTimeout(go, 520);
}

if (document.querySelector('[data-public-transition]')) {
    document.addEventListener('click', event => {
        const link = event.target.closest?.('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey
            || event.shiftKey || event.altKey || link.hasAttribute('download')
            || (link.target && link.target !== '_self') || !isPublicDestination(link.href)) return;
        event.preventDefault();
        navigatePublic(link.href);
    });
    window.addEventListener('pageshow', event => {
        navigating = false;
        clearTimeout(deadline);
        if (event.persisted) delete root.dataset.publicTransition;
    });
    window.addEventListener('pagehide', () => clearTimeout(deadline));
}
