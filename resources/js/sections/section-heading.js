// Scroll starts the sequence, but only animation completion advances it.
export function mountSectionHeading(root, name, duration, travel) {
    const heading = root.querySelector(`[data-${name}-heading]`);
    const lines = [...heading.querySelectorAll(`[data-${name}-heading-line]`)];
    const lower = lines.length > 1 ? heading.lastElementChild : null;
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    let state = 'idle';
    let version = 0;
    let animations = [];
    let suspended = false;
    let disposed = false;
    const setState = value => { state = value; heading.dataset.headingState = value; };
    const release = () => { animations.forEach(animation => animation.cancel()); animations = []; };
    const synchronize = () => animations.forEach(animation => {
        if (suspended || document.hidden) animation.pause();
        else animation.play();
    });
    const fallback = () => {
        version++;
        setState('static');
        heading.dataset.revealed = 'true';
        release();
    };
    const start = async () => {
        if (disposed || suspended || state !== 'idle') return;
        const attempt = ++version;
        setState('revealing');
        heading.dataset.revealed = 'true';
        try {
            animations = lines.map((line, index) => line.animate([
                { transform: `translateY(${index && lower ? -travel : travel}%)`, opacity: 0 },
                { transform: 'translateY(0%)', opacity: 1 },
            ], { duration, easing: 'cubic-bezier(.22, 1, .36, 1)', fill: 'both' }));
            synchronize();
            await Promise.all(animations.map(animation => animation.finished));
            if (disposed || attempt !== version) return;
            if (!lower) { setState('complete'); release(); return; }
            setState('shifting');
            release();
            const distance = name === 'program'
                ? 'calc(var(--program-heading-sign) * var(--program-heading-shift))'
                : 'calc(var(--values-heading-sign) * var(--values-heading-shift))';
            animations = [lower.animate([
                { transform: 'translateX(0px)' }, { transform: `translateX(${distance})` },
            ], { duration, easing: 'cubic-bezier(.22, 1, .36, 1)', fill: 'both' })];
            synchronize();
            await animations[0].finished;
            if (disposed || attempt !== version) return;
            setState('complete');
            release();
        } catch {
            if (!disposed && attempt === version) fallback();
        }
    };
    const trigger = () => {
        if (state !== 'idle' || suspended || document.hidden) return;
        const box = heading.getBoundingClientRect();
        if (box.top < document.documentElement.clientHeight * .88 && box.bottom > 0) start();
    };
    const configure = () => {
        if (motion.matches || !('IntersectionObserver' in window) || !heading.animate) fallback();
        else if (state === 'idle') {
            heading.dataset.revealed = 'false';
            setState('idle');
            trigger();
        }
    };
    window.addEventListener('scroll', trigger, { passive: true, signal });
    window.addEventListener('resize', trigger, { signal });
    document.addEventListener('visibilitychange', () => { synchronize(); trigger(); }, { signal });
    motion.addEventListener('change', configure, { signal });
    configure();
    return {
        suspend() { suspended = true; synchronize(); },
        resume() { suspended = false; synchronize(); trigger(); },
        dispose() {
            disposed = true; version++; release(); lifecycle.abort();
            delete heading.dataset.headingState; delete heading.dataset.revealed;
        },
    };
}
