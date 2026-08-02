import { DepthGalleryRenderer } from './renderer.js';

const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

function mixColor(first, second, amount) {
    return first.map((channel, index) => channel + (second[index] - channel) * amount);
}

export function readPalettes(cards) {
    return cards.map((card) => {
        const style = getComputedStyle(card);
        return {
            background: DepthGalleryRenderer.color(style.getPropertyValue('--depth-bg') || '#c78f2b'),
            blobA: DepthGalleryRenderer.color(style.getPropertyValue('--depth-blob-a') || '#ffd166'),
            blobB: DepthGalleryRenderer.color(style.getPropertyValue('--depth-blob-b') || '#f4a261'),
        };
    });
}

export function blendPalette(palettes, camera) {
    const lastIndex = Math.max(0, palettes.length - 1);
    const currentIndex = Math.min(lastIndex, Math.max(0, Math.floor(camera)));
    const nextIndex = Math.min(lastIndex, currentIndex + 1);
    const amount = clamp(camera - currentIndex);
    const current = palettes[currentIndex];
    const next = palettes[nextIndex] || current;

    return {
        background: mixColor(current.background, next.background, amount),
        blobA: mixColor(current.blobA, next.blobA, amount),
        blobB: mixColor(current.blobB, next.blobB, amount),
    };
}

export function sceneProgress(journey, viewport) {
    const rect = journey.getBoundingClientRect();
    const travel = Math.max(1, journey.offsetHeight - viewport.clientHeight);
    return clamp(-rect.top / travel);
}

export function updateDepthItems(items, camera, viewportWidth, pointer) {
    const horizontal = clamp(viewportWidth * 0.065, 18, 118);
    const activeIndex = Math.round(camera);

    items.forEach((item, index) => {
        const card = item.querySelector('[data-depth-gallery-card]');
        const relative = index - camera;
        const distance = Math.abs(relative);
        const side = Number.parseFloat(card?.style.getPropertyValue('--depth-side') || '0');
        const x = side * horizontal + pointer.x * 14 * Math.max(0, 1 - distance);
        const y = relative * 24 + pointer.y * 10 * Math.max(0, 1 - distance);
        const z = -relative * 650;
        const scale = Math.max(0.8, 1 - distance * 0.07);
        const rotate = -side * relative * 3.5;
        const opacity = clamp(1 - distance * 0.82);

        item.style.transform = [
            'translate3d(-50%, -50%, 0)',
            `translate3d(${x.toFixed(2)}px, ${y.toFixed(2)}px, ${z.toFixed(2)}px)`,
            `rotateY(${rotate.toFixed(2)}deg)`,
            `scale(${scale.toFixed(4)})`,
        ].join(' ');
        item.style.opacity = opacity.toFixed(4);
        item.style.pointerEvents = distance < 0.58 ? 'auto' : 'none';

        if (card) {
            card.tabIndex = index === activeIndex ? 0 : -1;
            card.toggleAttribute('aria-current', index === activeIndex);
        }
    });

    return activeIndex;
}

export function clearDepthItems(items) {
    items.forEach((item) => {
        item.removeAttribute('style');
        item.querySelector('[data-depth-gallery-card]')?.removeAttribute('tabindex');
        item.querySelector('[data-depth-gallery-card]')?.removeAttribute('aria-current');
    });
}
