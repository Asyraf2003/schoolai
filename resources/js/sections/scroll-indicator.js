export function scrollIndicatorOffset(scrollY, scrollHeight, viewportHeight, railHeight, thumbHeight) {
    const distance = Math.max(0, scrollHeight - viewportHeight);
    if (!distance) return 0;
    const progress = Math.min(1, Math.max(0, scrollY / distance));
    return Math.round(progress * Math.max(0, railHeight - thumbHeight));
}

export function mountScrollIndicator(root) {
    const thumb = root?.querySelector('[data-scroll-indicator-thumb]');
    if (!thumb) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const options = { passive: true, signal: lifecycle.signal };
    const html = document.documentElement;
    let suspended = document.hidden;
    let disposed = false;
    let frame = 0;
    let idleTimer = 0;
    let observer;

    function hide() {
        window.clearTimeout(idleTimer);
        idleTimer = 0;
        root.removeAttribute('data-scrolling');
    }
    function paint() {
        frame = 0;
        if (suspended || disposed) return;
        const scrollHeight = (document.scrollingElement ?? html).scrollHeight;
        const scrollable = scrollHeight - window.innerHeight > 1;
        root.hidden = !scrollable;
        html.toggleAttribute('data-scroll-indicator-ready', scrollable);
        if (!scrollable) { hide(); return; }
        const offset = scrollIndicatorOffset(
            window.scrollY, scrollHeight, window.innerHeight,
            root.clientHeight, thumb.clientHeight,
        );
        thumb.style.transform = `translate3d(0, ${offset}px, 0)`;
    }
    function schedule() {
        if (!frame && !suspended && !disposed) frame = window.requestAnimationFrame(paint);
    }
    function scrolled() {
        if (suspended || disposed) return;
        schedule();
        if (root.hidden) return;
        root.setAttribute('data-scrolling', '');
        window.clearTimeout(idleTimer);
        idleTimer = window.setTimeout(() => {
            idleTimer = 0;
            root.removeAttribute('data-scrolling');
        }, 150);
    }
    function suspend() {
        suspended = true;
        hide();
        if (frame) window.cancelAnimationFrame(frame);
        frame = 0;
    }
    function resume() {
        suspended = document.hidden;
        if (!suspended) schedule();
    }

    window.addEventListener('scroll', scrolled, options);
    window.addEventListener('resize', schedule, options);
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) suspend();
        else resume();
    }, { signal: lifecycle.signal });
    if ('ResizeObserver' in window) {
        observer = new ResizeObserver(schedule);
        observer.observe(document.body);
    }
    schedule();
    return {
        suspend,
        resume,
        dispose() {
            if (disposed) return;
            disposed = true;
            suspend();
            observer?.disconnect();
            lifecycle.abort();
            html.removeAttribute('data-scroll-indicator-ready');
            root.hidden = true;
        },
    };
}
