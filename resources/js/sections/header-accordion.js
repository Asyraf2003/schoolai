// This adapter alone owns enhanced details open/height during compact transitions.
export function mountHeaderAccordion(groups) {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    let desired = null;
    let desktop = false;
    let visible = false;
    let generation = 0;
    const animations = new Map();
    function accessibility(group, open) {
        const panel = group.querySelector('.site-header__panel');
        if (panel) panel.inert = !open;
    }
    function cancel() {
        generation++;
        animations.forEach((animation, group) => {
            group.style.height = `${group.getBoundingClientRect().height}px`;
            animation.cancel();
        });
        animations.clear();
    }
    function settle() {
        cancel();
        groups.forEach(group => {
            group.open = group.dataset.panel === desired;
            accessibility(group, group.open);
            group.style.removeProperty('height');
            group.style.removeProperty('overflow');
        });
    }
    async function transition(group, open, token) {
        const summaryHeight = group.querySelector('summary').getBoundingClientRect().height;
        const start = group.getBoundingClientRect().height;
        group.open = true;
        accessibility(group, open);
        group.style.removeProperty('height');
        const end = open ? group.getBoundingClientRect().height : summaryHeight;
        group.style.overflow = 'hidden';
        const animation = group.animate([{ height: `${start}px` }, { height: `${end}px` }], {
            duration: 320, easing: 'cubic-bezier(.22,1,.36,1)', fill: 'both',
        });
        animations.set(group, animation);
        try { await animation.finished; } catch { return false; }
        if (generation !== token) return false;
        animations.delete(group);
        group.open = open;
        animation.cancel();
        group.style.removeProperty('height');
        group.style.removeProperty('overflow');
        return true;
    }
    async function run(token) {
        for (const group of groups.filter(group => group.open && group.dataset.panel !== desired)) {
            if (!await transition(group, false, token)) return;
        }
        if (generation !== token) return;
        const next = groups.find(group => group.dataset.panel === desired);
        if (next) await transition(next, true, token);
    }
    function sync(id, full, shown) {
        if (desired === id && desktop === full && visible === shown) return;
        desired = id;
        desktop = full;
        visible = shown;
        if (!visible || desktop || preference.matches || !Element.prototype.animate) { settle(); return; }
        cancel();
        run(generation);
    }
    preference.addEventListener('change', settle);
    window.addEventListener('resize', settle, { passive: true });
    return {
        sync,
        dispose() {
            desired = null;
            settle();
            groups.forEach(group => accessibility(group, true));
            preference.removeEventListener('change', settle);
            window.removeEventListener('resize', settle);
        },
    };
}
