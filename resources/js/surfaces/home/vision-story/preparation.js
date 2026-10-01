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

function waitForVisionFonts(root) {
    if (!document.fonts?.load || !document.fonts?.check) return document.fonts?.ready;
    const requests = new Map();
    root.querySelectorAll('.vision-arch__heading, .vision-arch__description').forEach(element => {
        const style = window.getComputedStyle(element);
        const font = `${style.fontStyle} ${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;
        requests.set(font, (requests.get(font) || '') + element.textContent);
    });
    return Promise.all(Array.from(requests, ([font, text]) => (
        document.fonts.check(font, text) ? Promise.resolve() : document.fonts.load(font, text)
    )));
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
    const styles = waitForStyle(signal).then(() => {
        if (!signal?.aborted) root.dataset.visionStyleReady = 'true';
    });
    const fonts = styles.then(() => staticOnly ? undefined : waitForVisionFonts(root)).then(() => {
        if (!signal?.aborted) root.dataset.visionFontsReady = 'true';
    });
    const [media] = await Promise.all([
        preview ? prepareVisionPreview(preview, { signal, staticOnly: true }) : image?.decode?.(),
        fonts,
        styles,
    ]);
    return media || 'semantic-ready';
}
