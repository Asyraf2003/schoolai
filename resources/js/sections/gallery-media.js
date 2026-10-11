export function mountGalleryMedia(root) {
    const videos = [...root.querySelectorAll('[data-gallery-video]')];
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let suspended = false;
    let disposed = false;
    const pending = new Set();
    const release = video => {
        video.pause();
        if (video.hasAttribute('src')) { video.removeAttribute('src'); video.load(); }
        delete video.dataset.ready;
    };
    const sync = () => {
        const finished = root.hasAttribute('data-gallery-enabled') && +root.dataset.galleryProgress >= .90;
        const inactive = suspended || disposed || document.hidden || motion.matches || finished;
        const visible = inactive ? [] : videos.map(video => {
            const item = video.parentElement;
            const r = item.getBoundingClientRect();
            const opacity = parseFloat(getComputedStyle(item).opacity);
            const area = Math.max(0, Math.min(r.right, innerWidth) - Math.max(r.left, 0))
                * Math.max(0, Math.min(r.bottom, innerHeight) - Math.max(r.top, 0));
            return { video, area: opacity > .15 && !video.dataset.failed ? area : 0 };
        }).filter(entry => entry.area > 200).sort((a, b) => b.area - a.area).slice(0, 2).map(entry => entry.video);
        for (const video of videos) {
            if (!visible.includes(video)) { release(video); continue; }
            if (!video.hasAttribute('src')) { video.src = video.dataset.src; video.preload = 'metadata'; video.load(); }
            if (video.paused && !pending.has(video)) {
                pending.add(video);
                video.play()?.catch(() => { delete video.dataset.ready; }).finally(() => pending.delete(video));
            }
        }
    };
    for (const video of videos) {
        video.addEventListener('playing', () => { video.dataset.ready = 'true'; }, { signal });
        video.addEventListener('error', () => { if (video.hasAttribute('src')) video.dataset.failed = 'true'; release(video); }, { signal });
    }
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(sync, { threshold: [0, .1, .5] }) : null;
    const posters = 'IntersectionObserver' in window ? new IntersectionObserver(entries => {
        for (const entry of entries) {
            if (entry.isIntersecting && entry.target.matches('[data-gallery-poster]')) {
                entry.target.loading = 'eager';
                posters.unobserve(entry.target);
            }
        }
    }, { rootMargin: '100% 0px' }) : null;
    videos.forEach(video => observer?.observe(video));
    root.querySelectorAll('[data-gallery-poster]').forEach(poster => posters?.observe(poster));
    document.addEventListener('visibilitychange', sync, { signal });
    motion.addEventListener('change', sync, { signal });
    return {
        sync,
        suspend() { suspended = true; sync(); },
        resume() { suspended = false; sync(); },
        dispose() { disposed = true; sync(); observer?.disconnect(); posters?.disconnect(); lifecycle.abort(); },
    };
}
