// Header owns early preparation; existing image nodes and URLs survive reopening.
export function mountHeaderMedia(root) {
    const capability = matchMedia('(min-width: 768px)');
    const lifecycle = new AbortController();
    const frames = [...root.querySelectorAll('.site-header__media')];
    const prepared = new Set();
    const decoded = new WeakMap();
    let disposed = false;
    const settle = frame => {
        const image = frame.querySelector('[data-menu-media]');
        if (!image.complete) return;
        if (decoded.has(image)) return decoded.get(image);
        const preparation = (async () => {
            let usable = image.naturalWidth > 0;
            if (usable && image.decode) {
                try { await image.decode(); } catch { usable = false; }
            }
            if (disposed) return;
            frame.dataset.ready = String(usable);
            frame.dataset.mediaState = usable ? 'ready' : 'failed';
        })();
        decoded.set(image, preparation);
        return preparation;
    };
    const prime = () => {
        if (!capability.matches || disposed) return;
        frames.forEach(frame => {
            if (prepared.has(frame)) return;
            prepared.add(frame);
            if (!decoded.has(frame.querySelector('[data-menu-media]'))) frame.dataset.mediaState = 'preparing';
            const image = frame.querySelector('[data-menu-media]');
            image.fetchPriority = 'low';
            image.loading = 'eager';
            settle(frame);
        });
    };
    frames.forEach(frame => {
        const image = frame.querySelector('[data-menu-media]');
        frame.dataset.ready = 'false';
        image.addEventListener('load', () => settle(frame), { signal: lifecycle.signal });
        image.addEventListener('error', () => settle(frame), { signal: lifecycle.signal });
        if (image.complete && image.naturalWidth > 0) settle(frame);
    });
    capability.addEventListener('change', prime, { signal: lifecycle.signal });
    prime();
    return () => { disposed = true; lifecycle.abort(); prepared.clear(); };
}
