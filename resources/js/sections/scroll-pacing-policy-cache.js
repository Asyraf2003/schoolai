export function createPolicyCache(root, signal, measure) {
    const about = root.querySelector('[data-about]');
    const stories = [...(about?.querySelectorAll('[data-about-story]') ?? [])];
    const program = root.querySelector('[data-program]');
    const cards = [...(program?.querySelectorAll('[data-program-card]') ?? [])];
    const headings = ['program', 'values'].map(name => {
        const node = root.querySelector(`[data-${name}-heading]`);
        return { name, node, lines: node?.querySelectorAll(`[data-${name}-heading-line]`).length ?? 0 };
    });
    const values = root.querySelector('[data-values]');
    const track = values?.querySelector('[data-values-cards-track]');
    const viewport = values?.querySelector('[data-values-cards-viewport]');
    const valuesCards = [...(values?.querySelectorAll('[data-values-card]') ?? [])].map(node => ({
        node, flip: node.querySelector('[data-values-card-inner]'),
    }));
    const galleryNode = root.querySelector('[data-gallery]');
    const nodes = { about, stories, program, cards, headings, values, track, viewport, valuesCards };
    let snapshot;
    let galleryStart, galleryEnd, galleryShrink;
    const invalidate = () => { snapshot = null; };
    const resize = typeof ResizeObserver === 'function' ? new ResizeObserver(invalidate) : null;
    for (const node of [root, ...root.children, about, ...stories, program, ...cards, ...headings.map(h => h.node),
        values, track, viewport, ...valuesCards.map(card => card.node), galleryNode]) {
        if (node) resize?.observe(node);
    }
    const changes = new MutationObserver(invalidate);
    for (const [node, attributeFilter] of [
        [about, ['data-wide']],
        [values, ['data-line-ready', 'data-cards-flow']],
        [galleryNode, ['data-gallery-enabled', 'data-gallery-shrink-start']],
    ]) {
        if (node) changes.observe(node, { attributes: true, attributeFilter });
    }
    root.addEventListener('program:restored', invalidate, { capture: true, signal });
    document.fonts?.addEventListener('loadingdone', invalidate, { signal });
    signal.addEventListener('abort', () => { resize?.disconnect(); changes.disconnect(); }, { once: true });

    return {
        invalidate,
        read(gallery) {
            const start = gallery?.enabled ? gallery.start : undefined;
            const end = gallery?.enabled ? gallery.end : undefined;
            const shrink = galleryNode?.dataset.galleryShrinkStart;
            const changed = changes.takeRecords().length > 0;
            if (snapshot && !changed
                && start === galleryStart && end === galleryEnd && shrink === galleryShrink) return snapshot;
            galleryStart = start;
            galleryEnd = end;
            galleryShrink = shrink;
            snapshot = measure(nodes, start, end, Number(shrink));
            return snapshot;
        },
    };
}
