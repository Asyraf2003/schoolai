export function createOpeningProgress(gate, ledger) {
    const root = document.documentElement;
    const loader = document.querySelector('[data-home-opening]');
    const progress = loader?.querySelector('[data-home-opening-progress]');
    const percent = loader?.querySelector('[data-home-opening-percent]');
    let completion = null;
    let animation = null;
    const lifecycle = new AbortController();
    const active = () => new Promise(resolve => {
        if (gate.active()) { resolve(); return; }
        const resume = () => { if (!gate.active()) return; document.removeEventListener('schoolai:opening-active', resume); resolve(); };
        document.addEventListener('schoolai:opening-active', resume, { signal: lifecycle.signal });
    });
    const paint = () => new Promise(resolve => window.requestAnimationFrame(() => window.requestAnimationFrame(resolve)));
    function update() {
        const value = Math.floor(ledger.progress);
        if (progress) progress.value = value;
        if (percent) percent.textContent = `${value}%`;
        root.dataset.homeOpeningProgress = String(value);
        document.dispatchEvent(new CustomEvent('schoolai:opening-progress', { detail: { value, units: ledger.snapshot() } }));
    }
    async function finish() {
        if (!ledger.complete || gate.unlocked) return;
        await active();
        update();
        await paint();
        await active();
        if (gate.unlocked) return;
        const bounds = progress?.getBoundingClientRect();
        if (!loader || !percent || progress?.value !== 100 || !bounds?.width || !bounds?.height
            || window.getComputedStyle(progress).visibility !== 'visible') {
            root.dataset.homePreparationState = 'failed';
            loader?.querySelector('[data-home-opening-exit]')?.removeAttribute('hidden');
            return;
        }
        root.dataset.homeOpeningPhase = 'handoff';
        document.dispatchEvent(new CustomEvent('schoolai:opening-handoff'));
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (loader?.animate && !reduced) {
            animation = loader.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 160, easing: 'ease-out', fill: 'forwards' });
            await animation.finished.catch(() => {});
        }
        await active();
        gate.release({ complete: ledger.complete, painted: true, handoff: true });
    }
    document.addEventListener('schoolai:home-preparation', update, { signal: lifecycle.signal });
    document.addEventListener('visibilitychange', () => document.hidden ? animation?.pause() : animation?.play(), { signal: lifecycle.signal });
    window.addEventListener('pagehide', event => {
        animation?.pause();
        if (!event.persisted) { animation?.cancel(); lifecycle.abort(); }
    }, { signal: lifecycle.signal });
    window.addEventListener('pageshow', () => { if (!document.hidden) animation?.play(); }, { signal: lifecycle.signal });
    const primary = document.querySelector('.hero-cinema__cta');
    function syncPrimaryAccess() {
        if (!primary || !loader) return;
        const bounds = primary.getBoundingClientRect();
        loader.toggleAttribute('data-primary-outside', bounds.top < 0 || bounds.bottom > window.innerHeight);
    }
    const geometry = window.ResizeObserver ? new window.ResizeObserver(syncPrimaryAccess) : null;
    if (primary) geometry?.observe(primary);
    const hero = document.querySelector('[data-hero-slider]');
    if (hero) geometry?.observe(hero);
    window.addEventListener('resize', syncPrimaryAccess, { signal: lifecycle.signal });
    lifecycle.signal.addEventListener('abort', () => geometry?.disconnect(), { once: true });
    syncPrimaryAccess();
    update();
    return { update, complete() { return completion ||= finish(); } };
}
