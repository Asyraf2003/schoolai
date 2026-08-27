export function galleryStoryVisual(item) {
    const media = item.querySelector('[data-gallery-story-media]');
    const visual = media?.querySelector('[data-gallery-story-visual]');
    return media && visual ? { media, visual } : null;
}

function fitImage(media, visual) {
    if (!(visual instanceof HTMLImageElement) || !visual.naturalWidth) return;

    media.style.removeProperty('width');
    media.style.removeProperty('height');
    visual.style.removeProperty('width');
    visual.style.removeProperty('height');
    if (!media.clientWidth || !media.clientHeight) return;

    const scale = Math.min(
        media.clientWidth / visual.naturalWidth,
        media.clientHeight / visual.naturalHeight,
    );
    const fittedWidth = visual.naturalWidth * scale;
    const fittedHeight = visual.naturalHeight * scale;

    media.style.width = `${fittedWidth.toFixed(2)}px`;
    media.style.height = `${fittedHeight.toFixed(2)}px`;
    visual.style.width = `${fittedWidth.toFixed(2)}px`;
    visual.style.height = `${fittedHeight.toFixed(2)}px`;
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
