const clamp = value => Math.max(0, Math.min(1, value));

// The blue one-third canvas uses native document scrolling. Once the reader
// scrolls through its short runway, continue to the actual Gallery page.
export function mountFooterNext(root) {
    const shell = root?.querySelector('[data-footer-next-shell]');
    const panel = shell?.querySelector('[data-footer-next]');
    const link = shell?.querySelector('[data-footer-next-main]');
    if (!shell || !panel || !link) return { suspend() {}, resume() {}, dispose() {} };
    const controller = new AbortController();
    const { signal } = controller;
    const reduce = matchMedia('(prefers-reduced-motion: reduce)');
    let prior = scrollY, entered = false, suspended = false, disposed = false;
    let committed = false;
    const navigate = () => {
        if (disposed || suspended || committed || reduce.matches) return;
        committed = true;
        window.location.assign(link.href);
    };
    const frame = () => {
        const current = scrollY;
        const movingDown = current > prior + .5;
        prior = current;
        if (disposed || suspended) return;
        const rect = shell.getBoundingClientRect();
        const visible = rect.top < innerHeight && rect.bottom > 0;
        if (!visible) {
            entered = false;
            panel.removeAttribute('data-footer-next-active');
            return;
        }
        const travel = Math.max(1, shell.offsetHeight - innerHeight);
        const progress = clamp(-rect.top / travel);
        if (movingDown && progress < .8) entered = true;
        const armed = entered && progress >= .8;
        panel.toggleAttribute('data-footer-next-active', armed);
        if (armed && movingDown && progress >= .985) navigate();
    };
    window.addEventListener('scroll', frame, { passive: true, signal });
    window.addEventListener('resize', frame, { signal });
    panel.addEventListener('wheel', event => {
        if (event.deltaY > 12 && panel.hasAttribute('data-footer-next-active')
            && shell.getBoundingClientRect().bottom <= innerHeight + 8) navigate();
    }, { passive: true, signal });
    frame();
    return {
        suspend() { suspended = true; panel.removeAttribute('data-footer-next-active'); },
        resume() { suspended = false; prior = scrollY; frame(); },
        dispose() { disposed = true; controller.abort(); panel.removeAttribute('data-footer-next-active'); },
    };
}
