export function mountGalleryViewer(changed) {
    const dialog = document.querySelector('[data-gallery-viewer]');
    if (!dialog?.showModal) return;
    const stage = dialog.querySelector('[data-viewer-stage]');
    let opener;
    const reset = () => {
        stage.querySelector('video')?.pause();
        stage.replaceChildren();
        delete document.documentElement.dataset.galleryViewing;
        changed(false);
        opener?.focus({ preventScroll: true });
    };
    document.addEventListener('click', event => {
        const link = event.target.closest?.('[data-viewer-link]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        opener = link;
        const video = link.dataset.mediaKind === 'video';
        const media = document.createElement(video ? 'video' : 'img');
        media.src = link.href;
        if (video) {
            media.controls = true;
            media.playsInline = true;
            media.preload = 'metadata';
            if (link.dataset.poster) media.poster = link.dataset.poster;
            media.setAttribute('aria-label', link.dataset.caption);
        } else media.alt = link.dataset.caption;
        dialog.querySelector('#gallery-viewer-caption').textContent = link.dataset.caption;
        dialog.querySelector('[data-viewer-original]').href = link.href;
        stage.replaceChildren(media);
        changed(true);
        document.documentElement.dataset.galleryViewing = '';
        dialog.showModal();
        dialog.querySelector('[data-viewer-close]').focus();
    });
    dialog.querySelector('[data-viewer-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', reset);
    dialog.addEventListener('click', event => { if (event.target === dialog) dialog.close(); });
    window.addEventListener('pagehide', () => { if (dialog.open) dialog.close(); });
}
