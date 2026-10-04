// Image readiness only: no guessed network/device tiers. Existing text stays underneath.
export function mountMediaFallback(root) {
    const cleanups = [];
    root.querySelectorAll('[data-media-fallback]').forEach(frame => {
        const image = frame.querySelector('img');
        const update = () => { frame.dataset.ready = String(image.complete && image.naturalWidth > 0); };
        image.addEventListener('load', update);
        image.addEventListener('error', update);
        update();
        cleanups.push(() => {
            image.removeEventListener('load', update);
            image.removeEventListener('error', update);
        });
    });
    return () => cleanups.forEach(cleanup => cleanup());
}
