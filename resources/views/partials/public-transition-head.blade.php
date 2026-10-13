@vite(['resources/css/public-transition.css', 'resources/js/public-transition.js'])
<script nonce="{{ Vite::cspNonce() }}">
    (() => {
        try {
            const key = 'schoolai-public-transition';
            const entry = JSON.parse(sessionStorage.getItem(key) || 'null');
            sessionStorage.removeItem(key);
            if (!entry || entry.url !== location.href || Date.now() - entry.at > 15000
                || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            document.documentElement.dataset.publicTransition = 'enter';
            document.documentElement.dataset.transitionPalette = String(entry.palette % 4);
            // CSS owns the entrance and its fail-open completion, even if JS fails.
        } catch {}
    })();
</script>
