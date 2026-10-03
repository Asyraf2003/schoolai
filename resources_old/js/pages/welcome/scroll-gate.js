export function createHomepageScrollGate(onFallback, deadlineMs = 8000) {
    const root = document.documentElement;
    const hero = document.querySelector('[data-hero-slider]');
    const listeners = new AbortController();
    let settled = false;
    let deadline = null;
    let resolveFallback;
    const fallbackReady = new Promise(resolve => { resolveFallback = resolve; });

    function release(state = 'prepared') {
        if (settled) return;
        settled = true;
        window.clearTimeout(deadline);
        listeners.abort();
        root.dataset.homeScrollGate = 'unlocked';
        root.dataset.homeExperienceState = state;
        document.querySelector('[data-vision-story]')?.removeAttribute('aria-busy');
        performance.mark('schoolai:first-journey-ready');
        root.dispatchEvent(new CustomEvent('schoolai:first-journey-ready', { bubbles: true, detail: { state } }));
    }

    function fallback(reason) {
        if (settled) return;
        onFallback(reason);
        root.dataset.homeFallbackReason = reason;
        release('static-fallback');
        resolveFallback({ state: 'static-fallback', reason });
    }

    if (!hero || window.scrollY > 0 || window.location.hash) {
        fallback('position-or-anchor');
        return { release, fallbackReady };
    }

    root.dataset.homeScrollGate = 'locked';
    root.dataset.homeExperienceState = 'preparing';
    document.querySelector('[data-vision-story]')?.setAttribute('aria-busy', 'true');
    const options = { capture: true, signal: listeners.signal };
    document.addEventListener('click', event => {
        if (event.target.closest('[data-hero-audio]')) return;
        if (event.target.closest('a, #navbar button, [data-vision-video-open]')) fallback('access-intent');
    }, options);
    document.addEventListener('focusin', event => {
        if (event.target.closest('.skip-link')) return;
        if (!hero.contains(event.target) && !event.target.closest('#navbar')) fallback('focus-intent');
    }, options);
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') fallback('escape');
    }, options);
    window.addEventListener('hashchange', () => fallback('anchor'), options);
    window.addEventListener('pagehide', () => fallback('pagehide'), options);
    deadline = window.setTimeout(() => fallback('deadline'), deadlineMs);
    return { release, fallbackReady };
}
