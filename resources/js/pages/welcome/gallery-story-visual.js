export function galleryStoryVisual(item) {
    const media = item.querySelector('[data-gallery-story-media]');
    const visual = media?.querySelector('[data-gallery-story-visual]');
    return media && visual ? { media, visual } : null;
}

function fitImage(media, visual) {
    if (!(visual instanceof HTMLImageElement) || !visual.naturalWidth) return;

    const scale = Math.min(
        media.clientWidth / visual.naturalWidth,
        media.clientHeight / visual.naturalHeight,
    );
    visual.style.width = `${(visual.naturalWidth * scale).toFixed(2)}px`;
    visual.style.height = `${(visual.naturalHeight * scale).toFixed(2)}px`;
}

export function fitGalleryStoryVisuals(items, onReady) {
    items.forEach((item) => {
        const target = galleryStoryVisual(item);
        if (!target) return;
        const { media, visual } = target;

        if (visual instanceof HTMLImageElement && !visual.complete) {
            visual.addEventListener('load', () => {
                fitImage(media, visual);
                onReady();
            }, { once: true });
            return;
        }
        fitImage(media, visual);
    });
}
