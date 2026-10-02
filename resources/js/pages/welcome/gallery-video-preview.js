export function mountGalleryVideoPreviews(root) {
    const previews = [...root.querySelectorAll('[data-gallery-video-preview]')];
    const visible = new Set();
    const lifecycle = new AbortController();
    const options = { signal: lifecycle.signal };
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    let suspended = false;
    function sync() {
        previews.forEach(preview => {
            if (preview.dataset.homeMediaPreparing === 'true') return;
            if (visible.has(preview) && !suspended && !document.hidden && !reduced.matches
                && preview.dataset.galleryVideoState === 'frame-ready'
                && document.documentElement.dataset.homeScrollGate === 'unlocked') {
                if (preview.paused) preview.play()?.catch(() => {});
            }
            else preview.pause();
        });
    }
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        entries.forEach(({ target, isIntersecting }) => isIntersecting ? visible.add(target) : visible.delete(target));
        sync();
    }, { threshold: .01 }) : null;
    previews.forEach(preview => observer ? observer.observe(preview) : visible.add(preview));
    document.addEventListener('schoolai:first-journey-ready', sync, options);
    document.addEventListener('visibilitychange', sync, options);
    reduced.addEventListener('change', sync, options);
    const destroy = () => { suspended = true; sync(); lifecycle.abort(); observer?.disconnect(); };
    window.addEventListener('pagehide', event => { suspended = true; sync(); if (!event.persisted) destroy(); }, options);
    window.addEventListener('pageshow', () => { suspended = false; sync(); }, options);
    return destroy;
}
