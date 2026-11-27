export function mountGalleryMedia(root) {
    const videos = [...root.querySelectorAll('[data-gallery-video]')];
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let enabled = false;
    let suspended = false;
    let disposed = false;
    const areas = new Map();
    const active = new Set();
    const release = video => {
        active.delete(video);
        video.pause();
        video.removeAttribute('src');
        video.load();
        delete video.dataset.ready;
    };
    const sync = () => {
        const inactive = suspended || disposed || document.hidden || motion.matches || !enabled;
        const visible = inactive ? [] : [...areas]
            .filter(([video, area]) => area > 200 && !video.dataset.failed)
            .sort((a, b) => b[1] - a[1]).slice(0, 2).map(([video]) => video);
        for (const video of active) {
            if (!visible.includes(video)) release(video);
        }
        for (const video of visible) {
            if (active.has(video)) continue;
            active.add(video);
            video.preload = 'metadata';
            video.src = video.dataset.src;
            video.play()?.catch(() => {});
        }
    };
    for (const video of videos) {
        video.addEventListener('playing', () => {
            if (active.has(video) && !video.paused) video.dataset.ready = 'true';
        }, { signal });
        video.addEventListener('error', () => {
            if (!active.has(video) || !video.error) return;
            video.dataset.failed = 'true';
            release(video);
            sync();
        }, { signal });
    }
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        for (const { target, intersectionRect, isIntersecting } of entries) {
            if (isIntersecting) areas.set(target, intersectionRect.width * intersectionRect.height);
            else areas.delete(target);
        }
        sync();
    }, { threshold: [0, .05, .15, .35, .65] }) : null;
    const posters = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        for (const { isIntersecting, target } of entries) {
            if (!isIntersecting) continue;
            target.loading = 'eager';
            posters.unobserve(target);
        }
    }, { rootMargin: '100% 0px' }) : null;
    const observePlayback = () => {
        observer?.disconnect();
        areas.clear();
        if (enabled && !suspended && !disposed) videos.forEach(video => observer?.observe(video));
        sync();
    };
    root.querySelectorAll('[data-gallery-poster]').forEach(poster => posters?.observe(poster));
    document.addEventListener('visibilitychange', sync, { signal });
    return {
        sync() {
            const cutoff = Number(root.dataset.galleryMediaCutoff || .88);
            const value = root.hasAttribute('data-gallery-enabled') && Number(root.dataset.galleryProgress || 0) < cutoff;
            if (enabled === value) return;
            enabled = value;
            observePlayback();
        },
        suspend() { suspended = true; observePlayback(); },
        resume() { suspended = false; observePlayback(); },
        dispose() {
            disposed = true;
            sync();
            observer?.disconnect();
            posters?.disconnect();
            lifecycle.abort();
        },
    };
}
