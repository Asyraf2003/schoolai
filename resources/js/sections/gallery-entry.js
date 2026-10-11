export function mountGalleryEntry(root, invalidate) {
    const world = root.previousElementSibling;
    const viewport = world?.querySelector('[data-values-cards-viewport]');
    const track = viewport?.parentElement;
    const cards = [...(world?.querySelectorAll('[data-values-card]') ?? [])];
    const header = root.querySelector('.gallery__header');
    let disposed = false;
    let suspended = false;
    const observer = typeof ResizeObserver === 'function' ? new ResizeObserver(() => {
        if (!disposed && !suspended) invalidate();
    }) : null;
    [world, track, viewport, header, ...cards].filter(Boolean).forEach(node => observer?.observe(node));
    const measure = () => {
        if (!viewport || !track || !cards.length || suspended || disposed) return false;
        const boxes = cards.map(card => card.getBoundingClientRect());
        const viewportBox = viewport.getBoundingClientRect();
        const sticky = getComputedStyle(viewport).position === 'sticky';
        const bottom = sticky
            ? track.getBoundingClientRect().bottom + scrollY - viewport.offsetHeight
                + Math.max(...boxes.map(box => box.bottom - viewportBox.top))
            : Math.max(...boxes.map(box => box.bottom + scrollY));
        const cardHeight = Math.max(...cards.map(card => card.offsetHeight));
        const gap = Math.max(48, innerHeight - cardHeight / 2);
        const inset = parseFloat(getComputedStyle(header).paddingTop) || 0;
        const margin = bottom + gap - inset - (world.getBoundingClientRect().bottom + scrollY);
        const value = `${margin.toFixed(2)}px`;
        const changed = root.style.getPropertyValue('--gallery-entry-margin') !== value;
        root.style.setProperty('--gallery-entry-margin', value);
        root.dataset.galleryEntryGap = gap.toFixed(2);
        return changed;
    };
    return {
        measure,
        suspend() { suspended = true; },
        resume() { suspended = false; invalidate(); },
        dispose() {
            disposed = true; observer?.disconnect();
            root.style.removeProperty('--gallery-entry-margin');
            delete root.dataset.galleryEntryGap;
        },
    };
}
