import { prepareNavigationMenus } from './navigation-menus.js';
import { prepareVisionPreview } from './video-readiness.js';
import { prepareGalleryMedia } from './gallery-media-preparation.js';
import { prepareHeroCarouselMedia } from './hero-media-preparation.js';
import { releasePreviewFrames } from './preview-readiness.js';

export async function prepareHomepageNavigation() {
    await prepareNavigationMenus();
    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        const { initHomepageCursor } = await import('./cursor.js');
        const cleanup = initHomepageCursor({ prepareAll: true });
        await cleanup?.ready;
    }
    return { state: 'prepared' };
}

export async function prepareHomepageMedia(signal) {
    try {
        await prepareHeroCarouselMedia(signal);
        const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        for (const preview of document.querySelectorAll('[data-vision-video-preview]')) {
            await prepareVisionPreview(preview, { signal, staticOnly: reduced });
        }
        for (const media of document.querySelectorAll('[data-gallery-story-visual]')) {
            media.dataset.galleryMediaReady = await prepareGalleryMedia(media, signal);
        }
        document.documentElement.dataset.homeMediaReady = 'true';
        return { state: 'prepared' };
    } finally { releasePreviewFrames(); }
}

export function preparedSemanticSurface(selector) {
    const root = document.querySelector(selector);
    const ready = root && root.textContent.trim() && root.getBoundingClientRect().height > 0;
    return { state: ready ? 'prepared' : 'failed' };
}
