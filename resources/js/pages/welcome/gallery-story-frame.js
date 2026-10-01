import { galleryStoryVisual } from './gallery-story-visual.js';

const clamp = value => Math.max(0, Math.min(1, value));
const clampSigned = value => Math.max(-1, Math.min(1, value));
function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - (2 * progress));
}

export function readGalleryItems(items) {
    const viewportHeight = Math.max(window.innerHeight, 1);
    const viewportCenter = viewportHeight * 0.5;
    const revealStart = viewportHeight * 0.98;
    const revealDistance = viewportHeight * 0.62;
    let nearestDistance = Number.POSITIVE_INFINITY;
    let nearestBackground = '';
    const frames = [];
    items.forEach(item => {
        const target = galleryStoryVisual(item);
        if (!target) return;
        const { media, visual } = target;
        const boundRect = media.getBoundingClientRect();
        const visualHeight = visual.offsetHeight || visual.getBoundingClientRect().height;
        const visualTop = boundRect.top + ((boundRect.height - visualHeight) * 0.5);
        const raw = clamp((revealStart - visualTop) / revealDistance);
        const entrance = smoothstep(raw);
        const copyProgress = smoothstep(clamp((raw - 0.12) / 0.88));
        const mediaCenter = visualTop + (visualHeight * 0.5);
        const centerDelta = mediaCenter - viewportCenter;
        const signed = clampSigned(centerDelta / (viewportHeight * 0.52));
        const openness = smoothstep(1 - Math.abs(signed));
        const closingInset = 40 * (1 - openness);
        const topInset = signed >= 0 ? closingInset : 0;
        const bottomInset = signed < 0 ? closingInset : 0;
        const mediaY = (1 - entrance) * Math.min(190, viewportHeight * 0.22);
        const copyY = (1 - copyProgress) * Math.min(108, viewportHeight * 0.125);
        const distance = Math.abs(centerDelta);
        if (distance < nearestDistance) {
            nearestDistance = distance;
            nearestBackground = item.dataset.galleryBackground || '';
        }
        frames.push({ item, visual, mediaY, entrance, topInset, bottomInset, copyY, copyProgress });
    });
    return { frames, nearestBackground };
}
