<style nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-home-opening-gate-style>
  html[data-home-scroll-gate="locked"] { overflow: hidden; }
</style>
<script nonce="{{ \Illuminate\Support\Facades\Vite::cspNonce() }}" data-home-opening-bootstrap data-home-opening-recovery="{{ \Illuminate\Support\Facades\Vite::asset('resources/js/pages/welcome/opening-recovery.js') }}">
(() => {
    if (window.schoolaiHomeOpening) return;
    const root = document.documentElement;
    const recoveryUrl = document.currentScript?.dataset.homeOpeningRecovery;
    const lifecycle = new AbortController();
    const options = { capture: true, signal: lifecycle.signal };
    let unlocked = false;
    let adopted = false;
    let suspended = false;
    let disposed = false;
    let onFallback = null;
    const active = () => !disposed && !suspended && !document.hidden;
    function release(state, proof) {
        if (unlocked || !active()) return false;
        if (state === 'prepared' && (proof?.settled !== 5 || !proof?.painted || !proof?.handoff)) return false;
        unlocked = true;
        clearTimeout(deadline);
        root.dataset.homeScrollGate = 'unlocked';
        root.dataset.homeExperienceState = state;
        root.dataset.homeOpeningPhase = state === 'prepared' ? 'complete' : 'bypassed';
        performance.mark('schoolai:first-journey-ready');
        root.dispatchEvent(new CustomEvent('schoolai:first-journey-ready', { bubbles: true, detail: { state } }));
        return true;
    }
    function bypass(reason) {
        if (unlocked) return;
        root.dataset.homeFallbackReason = reason;
        onFallback?.(reason);
        release('access-bypassed');
    }
    const deadline = setTimeout(() => {
        if (adopted) onFallback?.('deadline');
        else {
            root.dataset.homeFallbackReason = 'runtime-unavailable';
            if (recoveryUrl) import(recoveryUrl).catch(() => {
                root.dataset.homeOpeningPhase = 'runtime-unavailable';
                document.querySelector('[data-home-opening-exit]')?.removeAttribute('hidden');
            });
        }
    }, 8000);
    window.schoolaiHomeOpening = {
        active,
        get unlocked() { return unlocked; },
        adopt(fallback) {
            if (adopted || disposed) return false;
            adopted = true;
            onFallback = fallback;
            root.dataset.homeOpeningOwner = 'runtime';
            root.dispatchEvent(new CustomEvent('schoolai:opening-adopted', { bubbles: true }));
            return true;
        },
        release(proof) { return release('prepared', proof); },
        bypass,
    };
    root.dataset.homeOpeningOwner = 'bootstrap';
    root.dataset.homeOpeningPhase = 'preparing';
    const history = performance.getEntriesByType('navigation')[0]?.type === 'back_forward';
    if (location.hash || scrollY > 0 || history) bypass('position-or-anchor');
    else root.dataset.homeScrollGate = 'locked';
    const normalInput = event => !event.target.closest?.('#navbar, dialog, [role="dialog"], :fullscreen');
    const block = event => {
        if (!unlocked && normalInput(event) && event.cancelable) event.preventDefault();
    };
    document.addEventListener('wheel', block, { ...options, passive: false });
    document.addEventListener('touchmove', block, { ...options, passive: false });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') bypass('escape');
        else if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', ' '].includes(event.key)
            && !event.target.closest?.('button, input, textarea, select, a, [contenteditable]')) block(event);
    }, options);
    document.addEventListener('click', event => {
        if (event.target.closest?.('[data-hero-audio]')) return;
        if (event.target.closest?.('a, #navbar button, [data-vision-video-open], [data-program-open]')) bypass('access-intent');
    }, options);
    document.addEventListener('focusin', event => {
        if (!event.target.closest?.('.skip-link, [data-hero-slider], #navbar, [data-home-opening]')) bypass('focus-intent');
    }, options);
    window.addEventListener('hashchange', () => bypass('anchor'), options);
    window.addEventListener('scroll', () => { if (!unlocked && scrollY > 0) bypass('restored-position'); }, options);
    window.addEventListener('pagehide', event => {
        suspended = true;
        if (!event.persisted) { disposed = true; clearTimeout(deadline); lifecycle.abort(); onFallback?.('disposed'); }
    }, options);
    window.addEventListener('pageshow', () => {
        suspended = false;
        if (!unlocked && scrollY > 0) bypass('restored-position');
        root.dispatchEvent(new CustomEvent('schoolai:opening-active', { bubbles: true }));
    }, options);
    document.addEventListener('visibilitychange', () => {
        if (active()) root.dispatchEvent(new CustomEvent('schoolai:opening-active', { bubbles: true }));
    }, options);
})();
</script>
