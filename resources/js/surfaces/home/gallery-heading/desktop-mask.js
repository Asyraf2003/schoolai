const SVG_NS = 'http://www.w3.org/2000/svg';
const BLIND_COUNT = 30;
const BLIND_DURATION = 0.5;
const BLIND_STAGGER = 0.02;

export const LAYER_DURATION = BLIND_DURATION + ((BLIND_COUNT - 1) * BLIND_STAGGER);
export const MASK_START = 0.04;

export function clamp(value) {
    return Math.max(0, Math.min(1, value));
}

function power3Out(value) {
    const progress = clamp(value);
    return 1 - ((1 - progress) ** 3);
}

export function smoothstep(value) {
    const progress = clamp(value);
    return progress * progress * (3 - 2 * progress);
}

export function readValuesExitProgress(world) {
    if (!world) return 0;
    const raw = getComputedStyle(world)
        .getPropertyValue('--values-gallery-exit-progress')
        .trim();
    const value = Number.parseFloat(raw);
    return Number.isFinite(value) ? clamp(value) : 0;
}

function createBlindSet(group, vbHeight) {
    if (!group) return [];
    group.replaceChildren();

    const rowHeight = vbHeight / BLIND_COUNT;
    const blinds = [];
    let currentY = 0;

    for (let index = 0; index < BLIND_COUNT; index += 1) {
        /* Repo Hiro-kiii menyusun blind dari bawah ke atas. */
        const centerY = vbHeight - (currentY + rowHeight / 2);
        const top = document.createElementNS(SVG_NS, 'rect');
        const bottom = document.createElementNS(SVG_NS, 'rect');

        [top, bottom].forEach((rect) => {
            rect.setAttribute('x', '0');
            rect.setAttribute('width', '100');
            rect.setAttribute('height', '0');
            /* Rect hitam menghapus cover blue dan membuka Gallery scene 2. */
            rect.setAttribute('fill', 'black');
            rect.setAttribute('shape-rendering', 'crispEdges');
        });

        top.setAttribute('y', centerY.toFixed(4));
        bottom.setAttribute('y', centerY.toFixed(4));
        group.append(top, bottom);

        blinds.push({
            top,
            bottom,
            centerY,
            halfHeight: rowHeight / 2,
        });
        currentY += rowHeight;
    }

    return blinds;
}

function configureMaskLayer(svg, hostWidth, hostHeight) {
    if (!svg || hostWidth <= 0 || hostHeight <= 0) return [];

    const vbWidth = 100;
    const vbHeight = (hostHeight / hostWidth) * vbWidth;
    svg.setAttribute('viewBox', `0 0 ${vbWidth} ${vbHeight}`);

    svg.querySelectorAll('[data-gallery-mask-base], [data-gallery-mask-fill]')
        .forEach((rect) => {
            rect.setAttribute('width', vbWidth.toFixed(4));
            rect.setAttribute('height', vbHeight.toFixed(4));
        });

    return createBlindSet(
        svg.querySelector('[data-gallery-mask-blinds]'),
        vbHeight,
    );
}

export function buildMaskState(section) {
    const host = section.querySelector('[data-gallery-mask-handoff]');
    if (!host) return null;

    const width = window.innerWidth || host.getBoundingClientRect().width;
    const height = window.innerHeight || host.getBoundingClientRect().height;
    const blinds = configureMaskLayer(
        host.querySelector('[data-gallery-mask-layer="values"]'),
        width,
        height,
    );

    return { host, blinds };
}

export function paintBlindTimeline(blinds, masterTime) {
    if (!blinds?.length) return;

    blinds.forEach((blind, index) => {
        const blindStart = index * BLIND_STAGGER;
        const local = power3Out(
            (masterTime - blindStart) / BLIND_DURATION,
        );
        const opening = blind.halfHeight * local;
        const height = opening > 0 ? opening + 0.01 : 0;

        blind.top.setAttribute('y', (blind.centerY - opening).toFixed(4));
        blind.top.setAttribute('height', height.toFixed(4));
        blind.bottom.setAttribute('y', blind.centerY.toFixed(4));
        blind.bottom.setAttribute('height', height.toFixed(4));
    });
}
