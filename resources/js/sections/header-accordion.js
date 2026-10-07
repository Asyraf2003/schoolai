// One details adapter owns compact flow and desktop reveal, with shared phases.
export function mountHeaderAccordion(groups, changed = () => {}) {
    const preference = window.matchMedia('(prefers-reduced-motion: reduce)');
    const duration = 720;
    const easing = 'cubic-bezier(.45,0,.2,1)';
    let desired = null;
    let desktop = false;
    let visible = false;
    let generation = 0;
    const animations = new Map();
    const panel = group => group.querySelector('.site-header__panel');
    const notify = () => changed(groups.find(group => group.open)?.dataset.panel ?? null);
    function accessibility(group, open) { if (panel(group)) panel(group).inert = !open; }
    function clean(group) {
        group.style.removeProperty('height');
        group.style.removeProperty('overflow');
        panel(group)?.style.removeProperty('clip-path');
    }
    function cancel() {
        generation++;
        animations.forEach(({ animation, element, full }) => {
            if (full) element.style.clipPath = getComputedStyle(element).clipPath;
            else element.style.height = `${element.getBoundingClientRect().height}px`;
            animation.cancel();
        });
        animations.clear();
    }
    function settle() {
        cancel();
        groups.forEach(group => {
            group.open = group.dataset.panel === desired;
            group.dataset.motion = group.open ? 'open' : 'closed';
            accessibility(group, group.open);
            clean(group);
        });
        notify();
    }
    async function transition(group, open, token) {
        const full = desktop;
        const element = full ? panel(group) : group;
        const wasOpen = group.open;
        const startHeight = group.getBoundingClientRect().height;
        const startClip = wasOpen ? element.style.clipPath || 'inset(0 0 0 0)' : 'inset(0 0 100% 0)';
        group.open = true;
        group.dataset.motion = open ? 'opening' : 'closing';
        accessibility(group, open);
        notify();
        group.style.removeProperty('height');
        const endHeight = open ? group.getBoundingClientRect().height : group.querySelector('summary').getBoundingClientRect().height;
        if (!full) group.style.overflow = 'hidden';
        const frames = full
            ? [{ clipPath: startClip }, { clipPath: open ? 'inset(0 0 0 0)' : 'inset(0 0 100% 0)' }]
            : [{ height: `${startHeight}px` }, { height: `${endHeight}px` }];
        const animation = element.animate(frames, { duration, easing, fill: 'both' });
        animations.set(group, { animation, element, full });
        try { await animation.finished; } catch { return false; }
        if (generation !== token) return false;
        animations.delete(group);
        group.open = open;
        group.dataset.motion = open ? 'open' : 'closed';
        animation.cancel();
        clean(group);
        notify();
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
        if (!visible || preference.matches || !Element.prototype.animate) { settle(); return; }
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
            groups.forEach(group => { accessibility(group, true); delete group.dataset.motion; });
            preference.removeEventListener('change', settle);
            window.removeEventListener('resize', settle);
        },
    };
}
