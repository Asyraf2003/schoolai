// Scroll can start this sequence; only animation completion advances it.
export function mountValuesHeading(root) {
    const heading = root.querySelector('[data-values-heading]');
    const lines = [...heading.querySelectorAll('[data-values-heading-line]')];
    const lower = heading.lastElementChild;
    const motion = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    let state = 'idle';
    let generation = 0;
    let animations = [];
    let suspended = false;
    let disposed = false;
    const setState = value => { state = value; heading.dataset.headingState = value; };
    const synchronize = () => {
        for (const animation of animations) {
            if (suspended || document.hidden) animation.pause();
            else animation.play();
        }
    };
    const release = () => { animations.forEach(animation => animation.cancel()); animations = []; };
    const fallback = () => {
        generation++;
        setState('static');
        heading.dataset.revealed = 'true';
        release();
    };
    const start = async () => {
        if (state !== 'idle' || disposed || suspended) return;
        const attempt = ++generation;
        setState('revealing');
        heading.dataset.revealed = 'true';
        try {
            animations = lines.map((line, index) => line.animate([
                { transform: `translateY(${index ? -108 : 108}%)`, opacity: 0 },
                { transform: 'translateY(0%)', opacity: 1 },
            ], { duration: 900, easing: 'cubic-bezier(.33, 1, .68, 1)', fill: 'both' }));
            synchronize();
            await Promise.all(animations.map(animation => animation.finished));
            if (disposed || attempt !== generation) return;
            setState('shifting');
            release();
            animations = [lower.animate([
                { transform: 'translateX(0px)' },
                { transform: 'translateX(calc(var(--values-heading-sign) * var(--values-heading-shift)))' },
            ], { duration: 880, easing: 'cubic-bezier(.22, 1, .36, 1)', fill: 'both' })];
            synchronize();
            await animations[0].finished;
            if (disposed || attempt !== generation) return;
            setState('complete');
            release();
        } catch {
            if (!disposed && attempt === generation) fallback();
        }
    };
    const trigger = () => {
        if (state === 'idle' && !document.hidden
            && heading.getBoundingClientRect().top <= document.documentElement.clientHeight * 1.08) start();
    };
    const configure = () => {
        if (motion.matches || !('IntersectionObserver' in window) || !heading.animate) fallback();
        else if (state === 'idle') {
            heading.dataset.revealed = 'false';
            setState('idle');
            trigger();
        }
    };
    const { signal } = lifecycle;
    window.addEventListener('scroll', trigger, { passive: true, signal });
    window.addEventListener('resize', trigger, { signal });
    document.addEventListener('visibilitychange', () => { synchronize(); trigger(); }, { signal });
    motion.addEventListener('change', configure, { signal });
    configure();
    return {
        suspend() { suspended = true; synchronize(); },
        resume() { suspended = false; synchronize(); trigger(); },
        dispose() { disposed = true; generation++; release(); lifecycle.abort(); delete heading.dataset.headingState; delete heading.dataset.revealed; },
    };
}
