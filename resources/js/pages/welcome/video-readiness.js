import { preparePreview } from './preview-readiness.js';

export async function prepareVisionPreview(preview, { signal, staticOnly = false } = {}) {
    const poster = preview.closest('[data-vision-visual]')?.querySelector('[data-vision-preview-poster]');
    if (poster) poster.loading = 'eager';
    const state = await preparePreview(preview, { source: preview.dataset.visionVideoSrc, poster, signal, staticOnly });
    preview.dataset.visionVideoState = state;
    preview.dataset.visionVideoHydrated = state === 'frame-ready' ? 'true' : 'false';
    return state;
}
