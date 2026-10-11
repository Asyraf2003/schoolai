import { storiesCheckpoints, storiesCopyPose, storiesWordPose, storiesMediaPose } from './stories-motion.js';

export function mountStoriesMedia(root) {
    const sequence = root?.querySelector('[data-stories-sequence]');
    const viewport = sequence?.querySelector('[data-stories-viewport]');
    const chapters = [...(sequence?.querySelectorAll('[data-stories-feature]') ?? [])];
    if (!viewport || chapters.length < 2) return { suspend() {}, resume() {}, dispose() {} };
    const copies = chapters.map(chapter => chapter.querySelector('[data-stories-copy]'));
    const words = copies.map(copy => [...copy.querySelectorAll('[data-stories-word]')]);
    const videos = chapters.map(chapter => chapter.querySelector('[data-stories-video]'));
    const mobile = matchMedia('(max-width: 767px)');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const capable = 'IntersectionObserver' in window && CSS.supports('position', 'sticky');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const pending = new Set();
    let layout, near = false, enabled = false, suspended = false, disposed = false, frame = 0;

    const release = video => {
        video.pause();
        if (video.hasAttribute('src')) {
            video.removeAttribute('src');
            video.load();
        }
        delete video.dataset.ready;
    };
    const load = video => {
        if (video.dataset.failed || video.hasAttribute('src')) return;
        video.src = video.dataset.src;
        video.preload = 'metadata';
        video.load();
    };
    const play = video => {
        load(video);
        if (video.dataset.failed || !video.paused || pending.has(video)) return;
        pending.add(video);
        Promise.resolve(video.play()).catch(() => {
            delete video.dataset.ready;
        }).finally(() => {
            pending.delete(video);
            if (disposed || suspended || document.hidden || reduced.matches || !near) video.pause();
        });
    };
    const syncMedia = (position, frameState) => {
        const { current } = frameState;
        const allowed = near && !suspended && !disposed && !document.hidden && !reduced.matches;
        videos.forEach((video, index) => {
            if (!allowed) {
                if (!near || reduced.matches || disposed) release(video);
                else video.pause();
            } else if (index === current) {
                play(video);
            } else {
                video.pause();
                const distance = index > current ? layout.centers[index] - position : Infinity;
                if (index === current + 1 && distance < layout.stageHeight * .8) load(video);
                else if (Math.abs(index - current) > 1) release(video);
            }
        });
    };
    const render = () => {
        frame = 0;
        if (!enabled || !layout || disposed) return;
        const position = Math.max(0, Math.min(layout.travel,
            -sequence.getBoundingClientRect().top));
        const stageHeight = layout.stageHeight;
        const state = storiesMediaPose(position, layout.centers, stageHeight, mobile.matches);
        sequence.dataset.storiesProgress = (position / Math.max(1, layout.travel)).toFixed(5);
        sequence.dataset.storiesActive = String(state.current + 1);
        chapters.forEach((chapter, index) => {
            const pose = storiesCopyPose(position, layout.centers[index],
                layout.anchors[index], layout.heights[index], stageHeight,
                layout.titleOffsets[index]);
            chapter.style.setProperty('--stories-layer', String(index * 2));
            chapter.style.setProperty('--stories-media-opacity', String(state.layers[index].opacity));
            chapter.style.setProperty('--stories-media-y', state.layers[index].y.toFixed(3) + '%');
            copies[index].style.setProperty('--stories-copy-y', pose.y.toFixed(2) + 'px');
            words[index].forEach((word, wordIndex) => {
                const reveal = storiesWordPose(pose.enters, wordIndex, words[index].length);
                word.style.setProperty('--stories-word-opacity', reveal.opacity.toFixed(3));
                word.style.setProperty('--stories-word-blur', reveal.blur.toFixed(2) + 'px');
            });
        });
        syncMedia(position, state);
    };
    const schedule = () => {
        if (enabled && !disposed && !suspended && near && !frame) frame = requestAnimationFrame(render);
    };
    const measure = () => {
        if (!enabled || disposed) return;
        const stageHeight = viewport.clientHeight;
        const heights = copies.map(copy => copy.offsetHeight);
        const titleOffsets = copies.map(copy => {
            const title = copy.querySelector('h3').getBoundingClientRect();
            const frame = copy.getBoundingClientRect();
            return title.top + title.height / 2 - frame.top - frame.height / 2;
        });
        layout = { ...storiesCheckpoints(heights, stageHeight, mobile.matches, titleOffsets),
            heights, titleOffsets, stageHeight };
        sequence.style.setProperty('--stories-travel', Math.ceil(layout.travel) + 'px');
        render();
    };
    const configure = () => {
        enabled = capable && !reduced.matches;
        sequence.toggleAttribute('data-stories-sequence-enabled', enabled);
        if (enabled) measure();
        else {
            cancelAnimationFrame(frame); frame = 0; layout = null;
            videos.forEach(release);
            sequence.style.removeProperty('--stories-travel');
            delete sequence.dataset.storiesProgress;
            delete sequence.dataset.storiesActive;
            chapters.forEach(chapter => {
                chapter.style.removeProperty('--stories-layer');
                chapter.style.removeProperty('--stories-media-opacity');
                chapter.style.removeProperty('--stories-media-y');
            });
            copies.forEach(copy => copy.style.removeProperty('--stories-copy-y'));
            words.flat().forEach(word => {
                word.style.removeProperty('--stories-word-opacity');
                word.style.removeProperty('--stories-word-blur');
            });
        }
    };
    videos.forEach(video => {
        video.addEventListener('playing', () => { video.dataset.ready = 'true'; }, { signal });
        video.addEventListener('error', () => {
            video.dataset.failed = 'true';
            release(video);
        }, { signal });
    });
    const observer = capable ? new IntersectionObserver(entries => {
        near = entries.some(entry => entry.isIntersecting);
        if (!near) videos.forEach(release);
        else schedule();
    }, { rootMargin: '75% 0px' }) : null;
    observer?.observe(sequence);
    const size = typeof ResizeObserver === 'function' ? new ResizeObserver(measure) : null;
    size?.observe(viewport);
    copies.forEach(copy => size?.observe(copy));
    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', measure, { signal });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) videos.forEach(video => video.pause());
        else render();
    }, { signal });
    mobile.addEventListener('change', measure, { signal });
    reduced.addEventListener('change', configure, { signal });
    document.fonts?.ready.then(() => { if (!disposed) measure(); });
    configure();
    return {
        suspend() { suspended = true; cancelAnimationFrame(frame); frame = 0; videos.forEach(video => video.pause()); },
        resume() { suspended = false; measure(); schedule(); },
        dispose() {
            disposed = true; cancelAnimationFrame(frame);
            videos.forEach(release);
            observer?.disconnect(); size?.disconnect(); lifecycle.abort();
        },
    };
}
