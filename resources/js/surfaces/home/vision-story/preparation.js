import { prepareVisionPreview } from '../../../pages/welcome/video-readiness.js';

function waitForStyle(signal) {
    const styles = Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
        .filter(style => style.href.includes('welcome-vision-waapi'));
    return Promise.all(styles.map(style => {
        if (style.sheet) return Promise.resolve();
        return new Promise((resolve, reject) => {
            const cleanup = () => {
                style.removeEventListener('load', load);
                style.removeEventListener('error', fail);
                signal?.removeEventListener('abort', fail);
            };
            const load = () => { cleanup(); resolve(); };
            const fail = () => { cleanup(); reject(new Error('Vision stylesheet unavailable')); };
            style.addEventListener('load', load, { once: true });
            style.addEventListener('error', fail, { once: true });
            signal?.addEventListener('abort', fail, { once: true });
            if (signal?.aborted) fail();
        });
    }));
}

export async function prepareVisionAssets(root, signal, staticOnly) {
    root.classList.add('is-preparing');
    const preview = root.querySelector('[data-vision-video-preview]');
    const image = root.querySelector('[data-vision-art][data-lazy-src]');
    if (image?.dataset.lazySrc) {
        image.dataset.lazyHydrated = '1';
        image.loading = 'eager';
        image.src = image.dataset.lazySrc;
        image.removeAttribute('data-lazy-src');
    }
    const [media] = await Promise.all([
        preview ? prepareVisionPreview(preview, { signal, staticOnly }) : image?.decode?.(),
        staticOnly ? Promise.resolve() : document.fonts?.ready,
        waitForStyle(signal),
    ]);
    return media || 'semantic-ready';
}
