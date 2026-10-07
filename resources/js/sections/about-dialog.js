export function mountAboutDialog(root, changed, signal) {
    const dialog = root.querySelector('[data-about-dialog]');
    const player = dialog.querySelector('video');
    const close = root.querySelector('[data-about-close]');
    let opener;
    const release = () => {
        player.pause();
        player.removeAttribute('src');
        player.load();
    };
    const supported = typeof dialog.showModal === 'function';
    root.querySelectorAll('[data-about-open]').forEach(button => { button.hidden = !supported; });
    dialog.addEventListener('close', () => {
        release();
        if (opener?.isConnected && opener.getClientRects().length) opener.focus({ preventScroll: true });
        changed(false);
    }, { signal });
    close.addEventListener('click', () => dialog.close(), { signal });
    dialog.addEventListener('click', event => {
        if (event.target !== dialog) return;
        const bounds = dialog.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right
            || event.clientY < bounds.top || event.clientY > bounds.bottom) dialog.close();
    }, { signal });
    return {
        supported,
        open(story, trigger) {
            if (!supported || !story.dataset.full || dialog.open) return;
            opener = trigger;
            dialog.setAttribute('aria-label', story.dataset.openLabel);
            changed(true);
            dialog.showModal();
            player.src = story.dataset.full;
            player.muted = false;
            player.load();
            player.play().catch(() => {});
        },
        suspend() {
            if (dialog.open) dialog.close();
            release();
        },
    };
}
