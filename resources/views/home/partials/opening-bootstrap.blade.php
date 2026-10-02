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
    let pendingHash = location.hash;
    let restoredY = scrollY;
    const blockedContent = new Map();
    const active = () => !disposed && !suspended && !document.hidden;
    function release(state, proof) {
        if (unlocked || !active()) return false;
        if (!proof?.complete || !proof?.painted || !proof?.handoff) return false;
        unlocked = true;
        clearTimeout(deadline);
        root.dataset.homeScrollGate = 'unlocked';
        root.dataset.homeExperienceState = state;
        root.dataset.homeOpeningPhase = 'complete';
        blockedContent.forEach((inert, element) => { element.inert = inert; });
        if (pendingHash && pendingHash !== '#') {
            let id = pendingHash.slice(1);
            try { id = decodeURIComponent(id); } catch {}
            document.getElementById(id)?.scrollIntoView();
        }
        else if (restoredY > 0) window.scrollTo(0, restoredY);
        performance.mark('schoolai:first-journey-ready');
        root.dispatchEvent(new CustomEvent('schoolai:first-journey-ready', { bubbles: true, detail: { state } }));
        return true;
    }
    function holdContent() {
        document.querySelectorAll('main > :not([data-hero-slider]), .site-footer').forEach(element => {
            if (!blockedContent.has(element)) blockedContent.set(element, element.inert);
            element.inert = true;
        });
        if (!unlocked && scrollY > 0) window.scrollTo({ top: 0, behavior: 'instant' });
    }
    const deadline = setTimeout(() => {
        if (adopted) document.querySelector('[data-home-opening-exit]')?.removeAttribute('hidden');
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
    };
    root.dataset.homeOpeningOwner = 'bootstrap';
    root.dataset.homeOpeningPhase = 'preparing';
    root.dataset.homeScrollGate = 'locked';
    document.addEventListener('DOMContentLoaded', holdContent, { once: true, ...options });
    const normalInput = event => !event.target.closest?.('#navbar, dialog, [role="dialog"], :fullscreen');
    const block = event => {
        if (!unlocked && normalInput(event) && event.cancelable) event.preventDefault();
    };
    document.addEventListener('wheel', block, { ...options, passive: false });
    document.addEventListener('touchmove', block, { ...options, passive: false });
    document.addEventListener('keydown', event => {
        if (['ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', ' '].includes(event.key)
            && !event.target.closest?.('button, input, textarea, select, a, [contenteditable]')) block(event);
    }, options);
    document.addEventListener('click', event => {
        if (unlocked) return;
        const link = event.target.closest?.('a[href^="#"]');
        if (!link) return;
        event.preventDefault();
        event.stopPropagation();
        pendingHash = link.getAttribute('href');
        root.dataset.homePendingAnchor = pendingHash;
        document.dispatchEvent(new CustomEvent('mobile-navigation:request-close', { detail: { immediate: true } }));
    }, options);
    window.addEventListener('hashchange', () => {
        if (!unlocked) { pendingHash = location.hash; window.scrollTo({ top: 0, behavior: 'instant' }); }
    }, options);
    window.addEventListener('scroll', () => {
        if (!unlocked && scrollY > 0) window.scrollTo({ top: 0, behavior: 'instant' });
    }, options);
    window.addEventListener('pagehide', event => {
        suspended = true;
        if (!event.persisted) { disposed = true; clearTimeout(deadline); lifecycle.abort(); onFallback?.('disposed'); }
    }, options);
    window.addEventListener('pageshow', () => {
        suspended = false;
        if (!unlocked) { restoredY = Math.max(restoredY, scrollY); holdContent(); }
        root.dispatchEvent(new CustomEvent('schoolai:opening-active', { bubbles: true }));
    }, options);
    document.addEventListener('visibilitychange', () => {
        if (active()) root.dispatchEvent(new CustomEvent('schoolai:opening-active', { bubbles: true }));
    }, options);
})();
</script>
