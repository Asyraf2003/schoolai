export const HERO_READY_EVENT = 'schoolai:hero-ready';

export function armHeroReadySignal(root) {
    let frame = 0;
    const cancel = (event) => {
        if (event?.persisted) return;
        window.cancelAnimationFrame(frame);
        window.removeEventListener('pagehide', cancel);
    };
    const finish = () => {
        window.removeEventListener('pagehide', cancel);
        if (root.dataset.heroReady === 'true') return;
        root.dataset.heroReady = 'true';
        root.dataset.heroShellReady = 'true';
        document.documentElement.dataset.heroReady = 'true';
        performance.mark('schoolai:hero-shell-ready');
        window.dispatchEvent(new CustomEvent(HERO_READY_EVENT, { detail: { reason: 'shell-painted' } }));
    };
    window.addEventListener('pagehide', cancel);
    frame = window.requestAnimationFrame(() => {
        frame = window.requestAnimationFrame(finish);
    });
    return cancel;
}
