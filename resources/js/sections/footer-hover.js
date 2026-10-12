export function mountFooterHover(root) {
    const image = root?.querySelector('[data-footer-visual-image]');
    const triggers = [...(root?.querySelectorAll('[data-footer-preview]') ?? [])];
    if (!image || !triggers.length) return { suspend() {}, resume() {}, dispose() {} };
    const lifecycle = new AbortController();
    const { signal } = lifecycle;
    const positions = {
        whatsapp: { x: '3%', y: '13%', rotate: '-18deg' },
        instagram: { x: 'calc(100% - min(25vw, 23rem) - 3%)', y: '10%', rotate: '14deg' },
        email: { x: '4%', y: '51%', rotate: '9deg' },
        facebook: { x: 'calc(100% - min(25vw, 23rem) - 4%)', y: '47%', rotate: '-16deg' },
    };
    let suspended = false;
    let active = null;
    const clear = () => {
        active = null;
        root.removeAttribute('data-footer-preview-active');
        image.removeAttribute('src');
    };
    image.addEventListener('error', () => {
        if (active?.dataset.footerPreview !== 'maps') return;
        const backup = active.dataset.footerPreviewFallback;
        if (backup && image.getAttribute('src') !== backup) image.src = backup;
    }, { signal });
    const activate = trigger => {
        if (!trigger || suspended || document.hidden) return;
        active = trigger;
        const type = trigger.dataset.footerPreview;
        const src = trigger.dataset.footerPreviewSrc;
        if (!src) return;
        const position = positions[type];
        if (position) {
            root.style.setProperty('--footer-art-x', position.x);
            root.style.setProperty('--footer-art-y', position.y);
            root.style.setProperty('--footer-art-rotate', position.rotate);
        } else {
            root.style.setProperty('--footer-art-rotate', '0deg');
        }
        if (image.getAttribute('src') !== src) image.src = src;
        root.setAttribute('data-footer-preview-active', type);
    };
    triggers.forEach(trigger => {
        trigger.addEventListener('pointerenter', () => activate(trigger), { signal });
        trigger.addEventListener('pointerleave', () => {
            if (document.activeElement !== trigger) clear();
        }, { signal });
        trigger.addEventListener('focusin', () => activate(trigger), { signal });
        trigger.addEventListener('focusout', clear, { signal });
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
