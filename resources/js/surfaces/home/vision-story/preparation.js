function boundedWait(work, timeoutMs) {
    const timeout = new Promise((resolve) => {
        window.setTimeout(resolve, timeoutMs);
    });
    return Promise.race([work, timeout]);
}

function hydrateImages(root) {
    root.querySelectorAll('[data-vision-art][data-lazy-src]').forEach((image) => {
        const source = image.dataset.lazySrc;
        if (!source) return;
        image.dataset.lazyHydrated = '1';
        image.src = source;
        image.removeAttribute('data-lazy-src');
    });
}

function decodeImages(root) {
    const images = Array.from(root.querySelectorAll('[data-vision-art]'));
    const work = Promise.allSettled(images.map((image) => {
        if (typeof image.decode !== 'function') return Promise.resolve();
        return image.decode();
    }));
    return boundedWait(work, 900);
}

function waitForFonts() {
    if (!document.fonts?.ready) return Promise.resolve();
    return boundedWait(document.fonts.ready, 700);
}

export async function prepareVisionAssets(root) {
    root.classList.add('is-preparing');
    hydrateImages(root);
    await Promise.all([decodeImages(root), waitForFonts()]);
}
