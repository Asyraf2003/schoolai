export function galleryStoryVisual(item) {
    const media = item.querySelector('[data-gallery-story-media]');
    const visual = media?.querySelector('[data-gallery-story-visual]');
    return media && visual ? { media, visual } : null;
}

function fitImage(media, visual) {
    if (!(visual instanceof HTMLImageElement) || !visual.naturalWidth) return false;

    media.style.removeProperty('width');
    media.style.removeProperty('height');
    visual.style.removeProperty('width');
    visual.style.removeProperty('height');
    if (!media.clientWidth || !media.clientHeight) return false;

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
    return true;
}

function fitVideo(media, visual) {
    if (!(visual instanceof HTMLVideoElement) || !visual.videoWidth || !visual.videoHeight) return false;

    media.style.removeProperty('width');
    media.style.removeProperty('height');
    visual.style.removeProperty('width');
    visual.style.removeProperty('height');
    if (!media.clientWidth || !media.clientHeight) return false;

    const scale = Math.min(
        media.clientWidth / visual.videoWidth,
        media.clientHeight / visual.videoHeight,
    );
    const fittedWidth = visual.videoWidth * scale;
    const fittedHeight = visual.videoHeight * scale;

    media.style.width = `${fittedWidth.toFixed(2)}px`;
    media.style.height = `${fittedHeight.toFixed(2)}px`;
    visual.style.width = `${fittedWidth.toFixed(2)}px`;
    visual.style.height = `${fittedHeight.toFixed(2)}px`;
    return true;
}

function fitMedia(media, visual) {
    return fitImage(media, visual) || fitVideo(media, visual);
}

const awaiting = new WeakSet();

export function fitGalleryStoryVisuals(items, onReady) {
    items.forEach((item) => {
        const target = galleryStoryVisual(item);
        if (!target) return;
        const { media, visual } = target;

        if (awaiting.has(visual)) return;
        if (visual instanceof HTMLImageElement && !visual.complete) {
            awaiting.add(visual);
            visual.addEventListener('load', () => {
                awaiting.delete(visual);
                fitMedia(media, visual);
                onReady();
            }, { once: true });
            return;
        }

        if (visual instanceof HTMLVideoElement && visual.readyState < 1) {
            awaiting.add(visual);
            visual.addEventListener('loadedmetadata', () => {
                awaiting.delete(visual);
                fitMedia(media, visual);
                onReady();
            }, { once: true });
            return;
        }

        fitMedia(media, visual);
    });
}
