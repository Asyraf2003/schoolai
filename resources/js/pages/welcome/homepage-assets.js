export function yieldHomepageWork() {
    if (window.scheduler?.yield) return window.scheduler.yield();
    return new Promise(resolve => window.setTimeout(resolve, 0));
}

export async function prepareHomepageStyles() {
    await Promise.all([...document.querySelectorAll('link[rel="stylesheet"]')].map(link => {
        if (link.sheet && (!link.media || link.media === 'all')) return;
        return new Promise((resolve, reject) => {
            link.addEventListener('load', resolve, { once: true });
            link.addEventListener('error', () => reject(new Error('Required homepage stylesheet failed')), { once: true });
        });
    }));
    return { state: 'prepared' };
}

export async function prepareHomepageFonts() {
    const fonts = new Map();
    for (const element of document.querySelectorAll('body *')) {
        const text = [...element.childNodes].filter(node => node.nodeType === Node.TEXT_NODE)
            .map(node => node.textContent).join('').trim();
        if (!text) continue;
        const style = getComputedStyle(element);
        const font = `${style.fontStyle} ${style.fontWeight} ${style.fontSize} ${style.fontFamily}`;
        fonts.set(font, (fonts.get(font) || '') + text);
    }
    if (document.fonts?.load) {
        for (const [font, text] of fonts) {
            await document.fonts.load(font, text);
            if (!document.fonts.check(font, text)) throw new Error('Required homepage font unavailable');
        }
        await document.fonts.ready;
    }
    document.documentElement.dataset.homeFontsReady = 'true';
    return { state: 'prepared' };
}

export async function decodeHomepageImage(image) {
    image.loading = 'eager';
    const source = image.dataset.lazySrc || image.dataset.src;
    if (source) {
        image.src = source;
        image.dataset.lazyHydrated = '1';
        image.removeAttribute('data-lazy-src');
        image.removeAttribute('data-src');
    }
    await image.decode();
    if (!image.complete || !image.naturalWidth) throw new Error('Required homepage image unavailable');
    image.classList.add('is-lazy-loaded');
    image.dataset.homeImageReady = 'true';
}

export async function prepareHomepageImages() {
    const images = [...document.images];
    // Small batches bound decode work while keeping Hero and navigation responsive.
    for (let index = 0; index < images.length; index += 4) {
        await Promise.all(images.slice(index, index + 4).map(decodeHomepageImage));
        await yieldHomepageWork();
    }
    const urls = new Set();
    const collect = value => {
        for (const match of value.matchAll(/url\(["']?([^"')]+)["']?\)/g)) urls.add(match[1]);
    };
    for (const element of document.querySelectorAll('body *')) {
        collect(element.getAttribute('style') || '');
        for (const pseudo of [null, '::before', '::after']) {
            const style = getComputedStyle(element, pseudo);
            collect(style.backgroundImage);
            collect(style.maskImage);
        }
    }
    const bodyStyle = getComputedStyle(document.body);
    for (const name of ['--home-cursor-default-image', '--home-cursor-interactive-image']) collect(bodyStyle.getPropertyValue(name));
    document.querySelectorAll('video[poster]').forEach(video => urls.add(video.poster));
    for (const url of urls) {
        const image = new Image();
        image.src = url;
        await image.decode();
        if (!image.naturalWidth) throw new Error('Required homepage visual unavailable');
    }
    document.documentElement.dataset.homeImagesReady = 'true';
    return { state: 'prepared' };
}

export async function prepareHomepageGeometry() {
    window.dispatchEvent(new Event('resize'));
    document.dispatchEvent(new CustomEvent('schoolai:homepage-geometry'));
    // Existing Vision owner debounces actual resize. Its completion is observable.
    const vision = document.querySelector('[data-vision-story]');
    if (vision?.dataset.visionGeometryPending === 'true') {
        await new Promise(resolve => vision.addEventListener('schoolai:vision-geometry-ready', resolve, { once: true }));
    }
    await new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)));
    document.documentElement.dataset.homeGeometryReady = 'true';
    return { state: 'prepared' };
}
