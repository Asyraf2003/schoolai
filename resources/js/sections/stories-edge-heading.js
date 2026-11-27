// Vertical edge heading that appears inside Stories sticky viewport during media scroll.
// LTR: top-left edge, bottom-to-top from center toward corner; RTL: top-right edge, top-to-bottom toward center.
// Materializes from blur to sharp on entry and tracks header reveal height dynamically.
export function mountStoriesEdgeHeading(root) {
    if (!root) return { suspend() {}, resume() {}, dispose() {} };
    const sequence = root.querySelector('[data-stories-sequence]');
    const viewport = sequence?.querySelector('[data-stories-viewport]');
    const sourceHeading = root.querySelector('[data-stories-heading]');
    if (!sequence || !viewport || !sourceHeading) return { suspend() {}, resume() {}, dispose() {} };

    const desktop = matchMedia('(min-width: 768px)');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const header = document.querySelector('[data-header]');

    const edge = document.createElement('div');
    edge.className = 'stories__edge-heading';
    edge.setAttribute('aria-hidden', 'true');
    edge.textContent = sourceHeading.getAttribute('aria-label') || sourceHeading.textContent.trim().replace(/\s+/g, ' ');
    viewport.appendChild(edge);

    let frame = 0, suspended = false, disposed = false, enabled = false;

    const headerOffset = () => {
        if (!header || header.dataset.concealed === 'true') return 0;
        return header.offsetHeight || 0;
    };

    const render = () => {
        frame = 0;
        if (!enabled || disposed || suspended) return;
        const rect = sequence.getBoundingClientRect();
        const vh = Math.max(1, window.innerHeight);
        if (rect.top >= vh || rect.bottom <= 0) {
            edge.style.opacity = '0';
            edge.style.filter = 'blur(14px)';
            return;
        }
        // Starts materializing once media scroll engages (-rect.top approaches 0..25%vh).
        const entry = Math.max(0, Math.min(1, (-rect.top + vh * .12) / (vh * .26)));
        const blur = (1 - entry) * 14;
        edge.style.setProperty('--stories-edge-offset', `${headerOffset()}px`);
        edge.style.opacity = entry.toFixed(3);
        edge.style.filter = blur > 0.15 ? `blur(${blur.toFixed(1)}px)` : 'none';
    };
    const schedule = () => {
        if (!frame && enabled && !disposed && !suspended) frame = requestAnimationFrame(render);
    };

    const headerObserver = header && typeof MutationObserver === 'function'
        ? new MutationObserver(() => {
            if (enabled && !disposed && !suspended) {
                edge.style.setProperty('--stories-edge-offset', `${headerOffset()}px`);
            }
        })
        : null;
    headerObserver?.observe(header, { attributes: true, attributeFilter: ['data-concealed', 'data-mobile-open', 'data-open'] });

    const configure = () => {
        const on = desktop.matches && !reduced.matches;
        enabled = on;
        edge.hidden = !on;
        if (on) render();
        else { edge.style.opacity = '0'; edge.style.filter = ''; }
    };

    window.addEventListener('scroll', schedule, { passive: true, signal });
    window.addEventListener('resize', schedule, { signal });
    desktop.addEventListener('change', configure, { signal });
    reduced.addEventListener('change', configure, { signal });
    configure();

    return {
        suspend() { suspended = true; cancelAnimationFrame(frame); frame = 0; },
        resume() { suspended = false; configure(); schedule(); },
        dispose() {
            disposed = true;
            cancelAnimationFrame(frame);
            headerObserver?.disconnect();
            edge.remove();
            lifecycle.abort();
        },
    };
}
