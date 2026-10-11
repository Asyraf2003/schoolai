export function mountFooterHover(root) {
    const image = root?.querySelector('[data-footer-visual-image]');
    const triggers = [...(root?.querySelectorAll('[data-footer-preview]') ?? [])];
    if (!image || !triggers.length) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const reduced = matchMedia('(prefers-reduced-motion: reduce)');
    let suspended = false;
    const clear = () => {
        root.removeAttribute('data-footer-preview-active');
        image.removeAttribute('src');
    };
    const activate = trigger => {
        if (suspended || document.hidden || !trigger) return;
        const type = trigger.dataset.footerPreview;
        const src = trigger.dataset.footerPreviewSrc;
        if (!src) return;
        if (image.getAttribute('src') !== src) image.src = src;
        const isMap = type === 'maps';
        const rotation = reduced.matches || isMap ? 0 : Math.round(Math.random() * 90 - 45);
        const x = isMap ? 24 : Math.round(5 + Math.random() * 49);
        const y = isMap ? 12 : Math.round(10 + Math.random() * 41);
        root.style.setProperty('--footer-art-rotate', rotation + 'deg');
        root.style.setProperty('--footer-art-x', x + '%');
        root.style.setProperty('--footer-art-y', y + '%');
        root.setAttribute('data-footer-preview-active', type);
    };
    triggers.forEach(trigger => {
        trigger.addEventListener('pointerenter', () => activate(trigger), { signal });
        trigger.addEventListener('pointerleave', () => {
            if (document.activeElement !== trigger) clear();
        }, { signal });
        trigger.addEventListener('focusin', () => activate(trigger), { signal });
        trigger.addEventListener('focusout', () => clear(), { signal });
    });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) clear();
    }, { signal });
    return {
        suspend() { suspended = true; clear(); },
        resume() { suspended = false; },
        dispose() { suspended = true; clear(); lifecycle.abort(); },
    };
}
