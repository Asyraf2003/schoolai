import { decodeHomepageImage } from './homepage-assets.js';
import { preparePreview } from './preview-readiness.js';

export async function prepareGalleryMedia(media, signal) {
    if (media instanceof HTMLImageElement) {
        await decodeHomepageImage(media);
        return 'image-ready';
    }
    if (!(media instanceof HTMLVideoElement)) return 'static-ready';
    const poster = media.poster ? new Image() : null;
    if (poster) poster.src = media.poster;
    const state = await preparePreview(media, {
        source: media.dataset.galleryVideoSrc, poster, signal,
        staticOnly: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
    });
    media.dataset.galleryVideoState = state;
    media.dataset.galleryVideoHydrated = state === 'frame-ready' ? 'true' : 'false';
    return state;
}
