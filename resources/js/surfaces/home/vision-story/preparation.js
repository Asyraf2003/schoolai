function boundedWait(work, timeoutMs) {
    const timeout = new Promise((resolve) => {
        window.setTimeout(resolve, timeoutMs);
    });
    return Promise.race([work, timeout]);
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

export async function prepareVisionAssets(root, prepareTypography) {
    root.classList.add('is-preparing');
    prepareTypography(root);
    await Promise.all([decodeImages(root), waitForFonts()]);
}
