const clamp = value => Math.max(0, Math.min(1, value));
const smooth = value => { const p = clamp(value); return p * p * (3 - 2 * p); };

export function storiesDesktopPose(progress, index, count, viewportHeight) {
    const transition = index === 0 ? 1 : smooth((progress - (index - .25)) / .5);
    const enter = index === 0 ? 1 : smooth((progress - (index - .12)) / .32);
    const exit = index === count - 1 ? 1 : 1 - smooth((progress - (index + .7)) / .25);
    return {
        media: transition,
        copy: enter * exit,
        y: Math.round(viewportHeight * (.12 * (1 - enter) - .26 * clamp(progress - index))),
    };
}

export function storiesMobilePose(progress, index) {
    return index === 0 ? 0 : 100 * (1 - smooth(progress - (index - .5)));
}

export function mountStoriesMedia(root) {
    const sequence = root?.querySelector('[data-stories-sequence]');
    const viewport = sequence?.querySelector('[data-stories-viewport]');
    const chapters = [...(sequence?.querySelectorAll('[data-stories-feature]') ?? [])];
    if (!viewport || chapters.length < 2) return { suspend() {}, resume() {}, dispose() {} };
    const videos = chapters.map(chapter => chapter.querySelector('[data-stories-video]'));
    const mobile = matchMedia('(max-width: 767px)');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const capable = 'IntersectionObserver' in window && CSS.supports('position', 'sticky');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const pending = new Set();
    let near = false, suspended = false, disposed = false, frame = 0;
    let enabled = false;

    const release = video => {
        video.pause();
        if (video.hasAttribute('src')) {
            video.removeAttribute('src');
            video.load();
        }
        delete video.dataset.ready;
    };
    const play = video => {
        if (video.dataset.failed) return;
        if (!video.hasAttribute('src')) {
            video.src = video.dataset.src;
            video.preload = 'metadata';
            video.load();
        }
        if (!video.paused || pending.has(video)) return;
        pending.add(video);
        Promise.resolve(video.play()).catch(() => {
            delete video.dataset.ready;
        }).finally(() => {
            pending.delete(video);
            if (disposed || suspended || document.hidden || reduced.matches || !near) video.pause();
        });
    };
    const render = () => {
        frame = 0;
        if (!enabled || disposed) return;
        const rect = sequence.getBoundingClientRect();
        const travel = Math.max(1, sequence.offsetHeight - viewport.offsetHeight);
        const progress = clamp(-rect.top / travel) * chapters.length;
        const small = mobile.matches;
        sequence.dataset.storiesProgress = progress.toFixed(4);
        chapters.forEach((chapter, index) => {
            chapter.style.setProperty('--stories-layer', String(index));
            if (small) {
                chapter.style.setProperty('--stories-layer-y', storiesMobilePose(progress, index).toFixed(3) + '%');
            } else {
                const pose = storiesDesktopPose(progress, index, chapters.length, viewport.clientHeight);
                chapter.style.setProperty('--stories-media-opacity', pose.media.toFixed(4));
                chapter.style.setProperty('--stories-copy-opacity', pose.copy.toFixed(4));
                chapter.style.setProperty('--stories-copy-y', pose.y + 'px');
            }
        });
        const base = Math.min(chapters.length - 1, Math.floor(progress));
        const primary = small ? Math.min(chapters.length - 1, Math.floor(progress + .5)) : base;
        const active = new Set([primary]);
        if (small && primary > 0 && progress < primary + .5) active.add(primary - 1);
        if (!small && base < chapters.length - 1 && progress > base + .65) active.add(base + 1);
        videos.forEach((video, index) => {
            if (!near || suspended || document.hidden || reduced.matches || video.dataset.failed) {
                if (!near || reduced.matches) release(video); else video.pause();
            } else if (active.has(index)) {
                play(video);
            } else {
                video.pause();
                if (Math.abs(index - primary) > 1) release(video);
            }
        });
    };
    const schedule = () => {
        if (!frame && enabled && !disposed && !suspended && (near || !('IntersectionObserver' in window))) {
            frame = requestAnimationFrame(render);
        }
    };
    const configure = () => {
        enabled = capable && !reduced.matches;
        sequence.toggleAttribute('data-stories-sequence-enabled', enabled);
        chapters.forEach(chapter => {
            chapter.style.removeProperty('--stories-media-opacity');
            chapter.style.removeProperty('--stories-copy-opacity');
            chapter.style.removeProperty('--stories-copy-y');
            chapter.style.removeProperty('--stories-layer-y');
        });
        if (enabled) render();
        else {
            cancelAnimationFrame(frame); frame = 0;
            videos.forEach(release);
            delete sequence.dataset.storiesProgress;
        }
    };
    for (const video of videos) {
        video.addEventListener('playing', () => { video.dataset.ready = 'true'; }, { signal });
        video.addEventListener('error', () => {
            video.dataset.failed = 'true';
            release(video);
        }, { signal });
    }
    const observer = capable ? new IntersectionObserver(entries => {
        near = entries.some(entry => entry.isIntersecting);
        if (!near) videos.forEach(release);
        else schedule();
    }, { rootMargin: '75% 0px' }) : null;
    observer?.observe(sequence);
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', schedule, { signal });
    document.addEventListener('visibilitychange', schedule, { signal });
    mobile.addEventListener('change', schedule, { signal });
    reduced.addEventListener('change', configure, { signal });
    configure();
    return {
        suspend() {
            suspended = true;
            cancelAnimationFrame(frame); frame = 0;
            videos.forEach(video => video.pause());
        },
        resume() { suspended = false; render(); schedule(); },
        dispose() {
            disposed = true;
            cancelAnimationFrame(frame);
            videos.forEach(release);
            observer?.disconnect();
            lifecycle.abort();
            sequence.removeAttribute('data-stories-sequence-enabled');
        },
    };
}
