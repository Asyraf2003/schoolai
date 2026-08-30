export function galleryStoryVisual(item) {
    const media = item.querySelector('[data-gallery-story-media]');
    const visual = media?.querySelector('[data-gallery-story-visual]');
    return media && visual ? { media, visual } : null;
}

function intrinsicSize(visual) {
    if (visual instanceof HTMLImageElement && visual.naturalWidth && visual.naturalHeight) {
        return { width: visual.naturalWidth, height: visual.naturalHeight };
    }

    if (visual instanceof HTMLVideoElement && visual.videoWidth && visual.videoHeight) {
        return { width: visual.videoWidth, height: visual.videoHeight };
    }

    return null;
}

function fitMedia(media, visual) {
    const size = intrinsicSize(visual);
    if (!size) return;

    media.style.removeProperty('width');
    media.style.removeProperty('height');
    visual.style.removeProperty('width');
    visual.style.removeProperty('height');
    if (!media.clientWidth || !media.clientHeight) return;

    const scale = Math.min(
        media.clientWidth / size.width,
        media.clientHeight / size.height,
    );
    const fittedWidth = size.width * scale;
    const fittedHeight = size.height * scale;

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
                fitMedia(media, visual);
                onReady();
            }, { once: true });
            return;
        }

        if (visual instanceof HTMLVideoElement && visual.readyState < 1) {
            visual.addEventListener('loadedmetadata', () => {
                fitMedia(media, visual);
                onReady();
            }, { once: true });
            return;
        }

        fitMedia(media, visual);
    });
}
