const NATIVE_TARGET = 'input, textarea, select, iframe, dialog, [role="dialog"], [contenteditable], [data-scroll-native]';
const BURST_GAP_MS = 100;

export function createNativeScrollCheck(signal) {
    let path = [];
    let scrollable = [];
    let lastWheel = -Infinity;
    const changes = new MutationObserver(invalidate);
    function invalidate() {
        changes.disconnect();
        path = [];
        scrollable = [];
        lastWheel = -Infinity;
    }
    signal.addEventListener('abort', invalidate, { once: true });
    return {
        invalidate,
        ownsScroll(event, now) {
            const next = [];
            for (const node of event.composedPath()) {
                if (node === document.body || node === document.documentElement) break;
                if (!(node instanceof Element)) continue;
                if (node.matches(NATIVE_TARGET)) { invalidate(); return true; }
                next.push(node);
            }
            if (now - lastWheel > BURST_GAP_MS || changes.takeRecords().length
                || next.length !== path.length || next.some((node, index) => node !== path[index])) {
                changes.disconnect();
                path = next;
                scrollable = path.filter(node => /^(auto|scroll)$/.test(getComputedStyle(node).overflowY));
                for (const node of path) changes.observe(node, { attributes: true });
                changes.observe(document.body, { attributes: true, attributeFilter: ['class', 'style'] });
                changes.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'style', 'dir'] });
            }
            lastWheel = now;
            // Content can start overflowing without changing its container's style.
            return scrollable.some(node => node.scrollHeight > node.clientHeight + 1);
        },
    };
}
