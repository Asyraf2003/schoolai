export function mountStoriesMedia(root) {
    const video = root?.querySelector('[data-stories-video]');
    if (!video) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    let visible = !('IntersectionObserver' in window);
    let suspended = false;
    let disposed = false;
    let pending = false;
    const sync = () => {
        const allowed = visible && !suspended && !disposed && !document.hidden
            && !reduced.matches && !video.dataset.failed;
        if (!allowed) {
            video.pause();
            if (reduced.matches) delete video.dataset.ready;
            return;
        }
        if (!video.hasAttribute('src')) {
            video.src = video.dataset.src;
            video.preload = 'metadata';
            video.load();
        }
        if (!video.paused || pending) return;
        pending = true;
        Promise.resolve(video.play()).catch(() => {
            delete video.dataset.ready;
        }).finally(() => {
            pending = false;
            if (suspended || disposed || document.hidden || reduced.matches || !visible) video.pause();
        });
    };
    video.addEventListener('playing', () => { video.dataset.ready = 'true'; }, { signal });
    video.addEventListener('error', () => {
        video.dataset.failed = 'true';
        delete video.dataset.ready;
        sync();
    }, { signal });
    const observer = 'IntersectionObserver' in window
        ? new IntersectionObserver(entries => {
            visible = entries.some(entry => entry.isIntersecting);
            sync();
        }, { rootMargin: '15% 0px' }) : null;
    observer?.observe(root.querySelector('[data-stories-feature]'));
    document.addEventListener('visibilitychange', sync, { signal });
    reduced.addEventListener('change', sync, { signal });
    sync();
    return {
        suspend() { suspended = true; sync(); },
        resume() { suspended = false; sync(); },
        dispose() {
            disposed = true;
            sync();
            observer?.disconnect();
            lifecycle.abort();
        },
    };
}
